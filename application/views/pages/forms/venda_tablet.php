<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $venda_tablet = $view->item( 'venda_tablet' ); ?>
<?php $view->component( 'aside' ); ?>
<div id="wrapper" class="wrapper show">
    <?php $view->component( 'navbar' ); ?>

    <?php echo form_open( 'vendas_tablet/salvar', [ 'class' => 'card container fade-in' ] )?>
        <?php $view->component( 'breadcrumb' ); ?>        
        <div class="page-header">
            <h2>Nova venda de tablet</h2>
        </div>
        <?php if( $venda_tablet ): ?>
            <input type="hidden" name="cod" value="<?php echo $venda_tablet->CodVendaTablet; ?>">
        <?php endif; ?><!-- id -->
        
        <div class="row">
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input  type="text" 
                            class="form-control cpf" 
                            id="cpf" 
                            name="cpf" 
                            required
                            value="<?php echo $venda_tablet && $venda_tablet->cpf ? mascara_cpf( $venda_tablet->cpf ) : ''; ?>"
                            placeholder="999.999.999-99">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tablet">Tablet</label>
                    <select id="tablet" name="tablet" class="form-control">
                        <option value="">-- Selecione --</option>
                        <?php foreach( $view->item( 'tablets' ) as $item ): ?>
                        <option value="<?php echo $item->CodTablet?>" 
                                <?php echo $venda_tablet && $venda_tablet->tablet == $item->CodTablet ? 'selected="selected"' : ''; ?>>
                        <?php echo $item->nome; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="quantidade">Quantidade</label>
                    <input  type="number" 
                            class="form-control" 
                            id="quantidade" 
                            name="quantidade" 
                            required
                            value="<?php echo $venda_tablet ? $venda_tablet->quantidade : ''; ?>"
                            placeholder="99">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                 <div class="form-group">
                    <label for="data">Data</label>
                    <input  type="date" 
                            class="form-control" 
                            id="data" 
                            name="data" 
                            required
                            value="<?php echo $venda_tablet ? $venda_tablet->data : ''; ?>"
                            placeholder="99">
                </div>
            </div>
        </div>

        <?php if( $view->item( 'errors' ) ): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-danger">
                    <b>Erro ao salvar</b>
                    <p><?php echo $view->item( 'errors' ); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <hr>
        <button class="btn btn-primary">Salvar</button>
        <a href="<?php echo site_url( 'vendas_tablet' ); ?>" class="btn btn-danger">Cancelar</a>
    <?php echo form_close(); ?> 
</div>