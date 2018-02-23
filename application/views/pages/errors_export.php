<table>
    <thead>
        <tr>
            <?php foreach( $view->item( 'header' ) as $value ): ?>
            <th><?= $value ?></th>
            <?php endforeach; ?>
            <th>MOTIVO</th>
        </tr>
    </thead>
    <tbody>
        <?php array_map( function ( $value ) {
            $json = json_decode( $value['line'], true );
            echo '<tr>';
            foreach( $json as $item ) echo '<td>'.$item.'</td>';
            echo '<td>'.$value['motivo'].'</td>';
            echo '</tr>';
        }, $view->item( 'lines' ) )?>
    </tbody>
</table>