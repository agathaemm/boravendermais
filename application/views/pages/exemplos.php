<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $cartao = $view->item( 'cartao' ); ?>
<?php $view->component( 'aside' ); ?>
<div id="wrapper" class="wrapper show">
    <?php $view->component( 'navbar' ); ?>

    <div class="row card container fade-in">
        <div class="col-sm-12">
            <div class="page-header">
                <h1>Exemplo de importação de <?= $view->item( 'entidade' ) ?></h1>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <a target="blank" href="<?= site_url( 'exemplos/export/'.$view->item('export_method'))?>" class="btn btn-success">
                        Exportar planilha exemplo
                    </a>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <?php foreach( $view->item( 'data' ) as $key => $value ): ?>
                                <th><?= $key ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach( $view->item( 'data' ) as $key => $value ): ?>
                                <td><small><?= $value ?></small></td>
                                <?php endforeach; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>