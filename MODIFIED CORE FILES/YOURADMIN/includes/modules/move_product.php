<?php
/**
 * @package admin
 * @copyright Copyright 2003-2019 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: move_product.php for CEON URI Mapping 2018-12-20 10:49:16Z webchills $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
$heading[] = array('text' => '<h4>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</h4>');
$contents = array('form' => zen_draw_form('products', FILENAME_CATEGORY_PRODUCT_LISTING, 'action=move_product_confirm&cPath=' . $cPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : ''), 'post', 'class="form-horizontal"') . zen_draw_hidden_field('products_id', $pInfo->products_id));
$contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));
$contents[] = array('text' => TEXT_INFO_CURRENT_CATEGORIES);
$contents[] = array('text' => '<strong>' . zen_output_generated_category_path($pInfo->products_id, 'product') . '</strong>');
$contents[] = array('text' => zen_draw_label(sprintf(TEXT_MOVE, $pInfo->products_name), 'move_to_category_id', 'class="control-label"') . zen_draw_pull_down_menu('move_to_category_id', zen_get_category_tree(), $current_category_id, 'class="form-control"'));
// BEGIN CEON URI MAPPING 1 of 1
require_once(DIR_WS_CLASSES . 'class.CeonURIMappingAdminProductPages.php');        
$ceon_uri_mapping_admin = new CeonURIMappingAdminProductPages();        
$ceon_uri_mapping_admin->addURIMappingFieldsToProductMoveFieldsArray((int) $pInfo->products_id);        
// END CEON URI MAPPING 1 of 1
$contents[] = array('align' => 'center', 'text' => '<button type="submit" class="btn btn-primary">' . IMAGE_MOVE . '</button> <a href="' . zen_href_link(FILENAME_CATEGORY_PRODUCT_LISTING, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '" class="btn btn-default" role="button">' . IMAGE_CANCEL . '</a>');
