<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $rede = $view->item( 'rede' ); ?>
<?php $view->component( 'aside' ); ?>
<div id="wrapper" class="wrapper show">
    <?php $view->component( 'navbar' ); ?>

    <?php echo form_open( 'redes/salvar', [ 'class' => 'card container fade-in' ] )?>
        <?php $view->component( 'breadcrumb' ); ?>        
        <div class="page-header">
            <h2>Nova rede</h2>
        </div>

        <?php if( $rede ): ?>
            <input type="hidden" name="cod" value="<?php echo $rede->CodRede; ?>">
        <?php endif; ?><!-- id -->

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input  type="text" 
                            name="nome" 
                            id="nome"
                            placeholder="Rede Exemplo"
                            value="<?= isset( $rede ) ? $rede->nome : '' ?>"
                            class="form-control">
                </div>
            </div>
        </div><!-- input do nome -->

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">Referência</label>
                    <input  type="text" 
                            name="ref" 
                            id="ref"
                            placeholder="30182"
                            value="<?= isset( $rede ) ? $rede->ref : '' ?>"
                            class="form-control">
                </div>
            </div>
        </div><!-- input do nome -->

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
        <a href="<?php echo site_url( 'redes' ); ?>" class="btn btn-danger">Cancelar</a>
    <?php echo form_close(); ?> 
</div>