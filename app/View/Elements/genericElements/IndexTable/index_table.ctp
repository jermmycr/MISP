<?php
    /*
     *  echo $this->element('/genericElements/IndexTable/index_table', array(
     *      'top_bar' => (
     *          // search/filter bar information compliant with ListTopBar
     *      ),
     *      'data' => array(
                // the actual data to be used
     *      ),
     *      'fields' => array(
     *          // field list with information for the paginator, the elements used for the individual cells, etc
     *      ),
     *      'title' => optional title,
     *      'description' => optional description,
     *      'primary_id_path' => path to each primary ID (extracted and passed as $primary to fields)
     *  ));
     *
     */
    if (!empty($data['title'])) {
        echo sprintf('<h2>%s</h2>', h($data['title']));
    }
    if (!empty($data['description'])) {
        echo sprintf(
            '<div>%s</div>',
            empty($data['description']) ? '' : h($data['description'])
        );
    }
    if (!empty($data['html'])) {
        echo sprintf('<div>%s</div>', $data['html']);
    }
    $skipPagination = isset($data['skip_pagination']) ? $data['skip_pagination'] : 0;
    if (!$skipPagination) {
        $paginationData = !empty($data['paginatorOptions']) ? $data['paginatorOptions'] : array();
        echo $this->element('/genericElements/IndexTable/pagination', array('paginationOptions' => $paginationData));
    }
    if (!empty($data['top_bar'])) {
        echo $this->element('/genericElements/ListTopBar/scaffold', array('data' => $data['top_bar']));
    }
    $rows = '';
    $row_element = isset($data['row_element']) ? $data['row_element'] : 'row';
    $options = isset($data['options']) ? $data['options'] : array();
    $actions = isset($data['actions']) ? $data['actions'] : array();
    $dblclickActionArray = isset($data['actions']) ? Hash::extract($data['actions'], '{n}[dbclickAction]') : array();
    $dbclickAction = '';
    foreach ($data['data'] as $k => $data_row) {
        $primary = null;
        if (!empty($data['primary_id_path'])) {
            $primary = Hash::extract($data_row, $data['primary_id_path'])[0];
        }
        if (!empty($dblclickActionArray)) {
            $dbclickAction = sprintf("changeLocationFromIndexDblclick(%s)", $k);
        }
        $rows .= sprintf(
            '<tr data-row-id="%s" %s %s>%s</tr>',
            h($k),
            empty($dbclickAction) ? '' : 'ondblclick="' . $dbclickAction . '"',
            empty($primary) ? '' : 'data-primary-id="' . $primary . '"',
            $this->element(
                '/genericElements/IndexTable/' . $row_element,
                array(
                    'k' => $k,
                    'row' => $data_row,
                    'fields' => $data['fields'],
                    'options' => $options,
                    'actions' => $actions,
                    'primary' => $primary
                )
            )
        );
    }
    $tbody = '<tbody>' . $rows . '</tbody>';
    echo sprintf(
        '<div style="%s">',
        isset($data['max_height']) ? sprintf('max-height: %s; overflow-y: auto; resize: both', $data['max_height']) : ''
    );
        echo sprintf(
            '<table class="table table-striped table-hover table-condensed">%s%s</table>',
            $this->element('/genericElements/IndexTable/headers', array('fields' => $data['fields'], 'paginator' => $this->Paginator, 'actions' => empty($data['actions']) ? false : true)),
            $tbody
        );
    echo '</div>';
    if (!$skipPagination) {
        echo $this->element('/genericElements/IndexTable/pagination_counter', $paginationData);
        echo $this->element('/genericElements/IndexTable/pagination', $paginationData);
    }
?>
