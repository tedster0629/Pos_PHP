<?php
function get_return_invoice_items_html($reference_no, $store_id = null)
{    
    $return_model = registry()->get('loader')->model('sellreturn');
    return $return_model->getInvoiceItemsHTML($reference_no, $store_id);
}

function get_return_reference_no_by_invoice_id($invoice_id, $store_id = null)
{    
    $return_model = registry()->get('loader')->model('sellreturn');
    return $return_model->getRefNoByInvoiceId($invoice_id, $store_id);
}