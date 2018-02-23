<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $view->getTitle(); ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php foreach( $view->css as $css ): ?>
        <link href="<?php echo $css; ?>" rel="stylesheet" media="screen"/>
        <?php endforeach; ?>
        <script>
            var Site = {
                url: '<?php echo site_url(); ?>'
            };
        </script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css">
    </head>
    <body>
        <?php $this->load->view( 'pages/'.$view->page ); ?>
        <?php foreach( $view->js as $js ): ?>
        <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
        
        <?php if( $success = flash( 'success' ) ): ?>
        <script>
            swal( 'Sucesso!', '<?= $success ?>', 'success' );
        </script>
        <?php endif; ?>

        <?php if( $error = flash( 'error' ) ): ?>
        <script>
            swal( 'Erro!', '<?= $error ?>', 'danger' );
        </script>
        <?php endif; ?>
    </body>
</html>