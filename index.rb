require 'securerandom'
require 'json'

include ActionView::Helpers::NumberHelper

class LivreBankslip

  include Rails.application.routes.url_helpers

  attr_reader :key, :token, :user, :payment, :url_hash

  def initialize(key, user, payment)
    @key = key
    @user = user
    @payment = payment
    @url_hash = SecureRandom.hex

    @errors = []
  end

  def self.generate!(key, user, payment)
    self.new(key, user, payment).generate
  end

  def generate
    @token = get_token
    
    r = Nokogiri::XML.parse(bankslip_request.body)
    
    {
      payment_id: payment.id,
      code: r.css("NIDBOL").text,
      url: r.css("URLPST").text,
      url_hash: url_hash,
      due_date: Date.parse(r.css("DATVCT").text),
      valid_date: Date.parse(r.css("DATVAL").text)
    }
  end

  private
  def get_token
    Nokogiri::XML.parse(token_request.body).css("USRTOK").text
  end

  def token_request
    client.post 'slip', form_token
  end

  def bankslip_request
    client.post 'slip', form_slip
  end
  
  def form_token
    if payment.ticket?
      due_date = payment.payable.duedate
    else
      due_date = Date.current + BANKSLIP_DUEDATE
    end

    valid_date = due_date + BANKSLIP_VALID_DATE

    {
      USRKEY: key,
      URLRET: LIVRE_RETURN_URL,
      NOMCED: BANKSLIP_FIRM_NAME,
      NOMSAC: user.name,
      SACMAL: user.email,
      CODCMF: user.registration,
      VLRBOL: number_with_precision(payment.value, precision: 2, delimiter: ''),
      DATVCT: I18n.l(due_date),
      DATVAL: I18n.l(valid_date),
    }
  end

  def form_slip
    if payment.ticket?
      due_date = payment.payable.duedate
    else
      due_date = Date.current + BANKSLIP_DUEDATE
    end

    valid_date = due_date + BANKSLIP_VALID_DATE

    {
      USRKEY: key,
      USRTOK: token,
      URLRET: LIVRE_RETURN_URL,
      NOMCED: BANKSLIP_FIRM_NAME,
      NOMSAC: user.name,
      SACMAL: user.email,
      CODCMF: user.registration,
      VLRBOL: number_with_precision(payment.value, precision: 2, delimiter: ''),
      DATVCT: I18n.l(due_date),
      DATVAL: I18n.l(valid_date),
    }
  end

  def client
    @conn ||= Faraday.new(url: LIVRE_ENDPOINT) do |faraday|
      faraday.request  :url_encoded
      faraday.response :logger
      faraday.adapter  Faraday.default_adapter
    end
  end

end