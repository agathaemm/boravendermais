<table>
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
            <td><?=  mb_convert_encoding( $value, 'UTF-16LE', 'UTF-8' ) ?></td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>