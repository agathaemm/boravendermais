<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $finder = $view->item( 'finder' ); ?>
<?php $view->component( 'aside' ); ?>
<div id="wrapper" class="wrapper show">
    <?php $view->component( 'navbar' ); ?>

    <div class="container">
        <?php $view->component( 'breadcrumb' ); ?>        
         <div class="row fade-in">
            <div class="col-md-12">
                <?php $view->component( 'filters' ); ?>
            </div>
        </div>
        <?php if ( $view->item( 'errors' ) ): ?>
        <div class="row fade-in">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <?php $erros = $view->item( 'errors' ); 
                           if( is_array( $erros ) ) : 
                                foreach ($erros as $key => $erro) {
                                    echo $erro.'<br>';
                                }
                            else :
                                echo $erros;
                            endif;
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $view->item( 'warnings' ) ): ?>
        <div class="row fade-in">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <?php $warnings = $view->item( 'warnings' ); 
                           if( is_array( $warnings ) ) : 
                                foreach ($warnings as $key => $warning) {
                                    echo $warning.'<br>';
                                }
                            else :
                                echo $warnings;
                            endif;
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row margin fade-in">
            
            <?php if ( $view->item( 'add_url' ) ): ?>        
            <div class="col-md-12">
                <a href="<?php echo $view->item( 'add_url' ); ?>" class="btn btn-primary z-depth-2">Adicionar</a> 
                <?php if ( $view->item( 'export_url' ) ): ?>
                    <a href="<?php echo $view->item( 'export_url' ); ?>" class="btn-info btn z-depth-2">
                        Exportar como XLS
                    </a>
                <?php endif; ?>
                <?php if ( $view->item( 'import_url' ) ): ?>
                <?php echo form_open_multipart( $view->item( 'import_url' ), [  'id' => 'import-form', 'style' => 'display: inline-block' ] ); ?>
                    <input  id="planilha" 
                            name="planilha" 
                            onchange="importarPlanilha( $( this ) )" 
                            class="planilha hidden" 
                            type="file">
                    <label for="planilha" class="btn btn-success z-depth-2">
                        Importar planilha
                    </label> 
                <?php echo form_close(); ?>
                <?php endif; ?>
                <?php if ( $view->item( 'example_url' ) ): ?>
                <a href="<?=  $view->item( 'example_url' ) ?>" target="blank" class="btn btn-warning z-depth-2">
                    Visualizar exemplo de importação
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="col-md-12"><hr></div>
        </div>
        
        <div class="row fade-in">
            <div class="col-md-12">
                <?php $view->component( 'table' ); ?>            
            </div>
        </div>  
    </div>   
</div>

<style>
@media 
only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)  {
    <?php $cont = 1; ?>
    <?php foreach( $view->getHeader( 'grid' ) as $row ): ?>
    td:nth-of-type(<?php echo $cont; ?>):before { content: "<?php echo $finder->getLabel( $row ); ?>"; }
    <?php $cont++;?>    
    <?php endforeach;?>
}
</style>