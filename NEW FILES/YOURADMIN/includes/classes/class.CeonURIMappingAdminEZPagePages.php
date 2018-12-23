<?php

/**
 * Ceon URI Mapping Zen Cart EZ-Pages Admin Functionality.
 *
 * This file contains a class which handles the functionality for EZ-page pages within the Zen Cart admin.
 *
 * @package     ceon_uri_mapping
 * @author      Conor Kerr <zen-cart.uri-mapping@ceon.net>
 * @copyright   Copyright 2008-2012 Ceon
 * @copyright   Copyright 2003-2019 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://ceon.net/software/business/zen-cart/uri-mapping
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version     $Id: class.CeonURIMappingAdminEZPagePages.php 2018-20-23 10:57:15Z webchills $
 */

if (!defined('IS_ADMIN_FLAG')) {
	die('Illegal Access');
}

/**
 * Load in the parent class if not already loaded
 */
require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.CeonURIMappingAdminEZPages.php');


// {{{ CeonURIMappingAdminEZPagePages

/**
 * Handles the URI mappings for EZ-pages.
 *
 * @package     ceon_uri_mapping
 * @author      Conor Kerr <zen-cart.uri-mapping@ceon.net>
 * @copyright   Copyright 2008-2012 Ceon
 * @copyright   Copyright 2003-2018 Zen Cart Development Team
 * @copyright   Portions Copyright 2003 osCommerce
 * @link        http://ceon.net/software/business/zen-cart/uri-mapping
 * @license     http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */
class CeonURIMappingAdminEZPagePages extends CeonURIMappingAdminEZPages
{
	// {{{ properties
		
	/**
	 * Maintains a copy of the current URI mappings entered/generated for the EZ-page.
	 *
	 * @var     array
	 * @access  protected
	 */
	public $_uri_mappings = array();
	
	/**
	 * Maintains a copy of any previous URI mappings for the EZ-page.
	 *
	 * @var     array
	 * @access  protected
	 */
	public $_prev_uri_mappings = array();
	
	/**
	 * Flag indicates whether or not auto-generation of URI mappings has been selected for the EZ-page.
	 *
	 * @var     boolean
	 * @access  protected
	 */
	public $_uri_mapping_autogen = false;
	
	/**
	 * Maintains a list of any URI mappings for the EZ-page which clash with existing mappings.
	 *
	 * @var     array
	 * @access  protected
	 */
	public $_clashing_mappings = array();
	
	// }}}
	
	
	// {{{ Class Constructor
	
	/**
	 * Creates a new instance of the CeonURIMappingAdminEZPagePages class.
	 * 
	 * @access  public
	 */
	function __construct()
	{
		// Load the language definition file for the current language
		@include_once(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . 'ceon_uri_mapping_ezpage_pages.php');
		
		if (!defined('CEON_URI_MAPPING_TEXT_EZPAGES_URI') && $_SESSION['language'] != 'english') {
			// Fall back to english language file
			@include_once(DIR_WS_LANGUAGES . 'english/' . 'ceon_uri_mapping_ezpage_pages.php');
		}
		
		parent::__construct();
	}
	
	// }}}
	
	
	// {{{ insertUpdateHandler()
	
	/**
	 * Handles the Ceon URI Mapping functionality when an EZ-page is being inserted or updated.
	 *
	 * @access  public
	 * @param   integer   $page_id   The ID of the EZ-page.
	 * @param   integer   $page_title   The name for the EZ-Page.
	 * @param   array     $page_titles_array   The names for the EZ-Page for the languages used by the store.
	 * @return  none
	 */
	public function insertUpdateHandler($page_id, $page_title, $page_titles_array = null)
	{
		global $messageStack;
		
		$uri_mapping_autogen = (isset($_POST['uri-mapping-autogen']) ? true : false);
		
		$languages = zen_get_languages();
		
		for ($i = 0, $n = count($languages); $i < $n; $i++) {
			$prev_uri_mapping = trim($_POST['prev-uri-mappings'][$languages[$i]['id']]);
			
			// Handle multilanguage EZ-Pages 
			if (null !== $page_titles_array) {
				$page_title = $page_titles_array[$languages[$i]['id']];
			}
			
			// Auto-generate the URI if requested
			if ($uri_mapping_autogen) {
				$uri_mapping = $this->autogenEZPageURIMapping((int) $page_id, $page_title,
					$languages[$i]['code'], $languages[$i]['id']);
				
				if ($uri_mapping == CEON_URI_MAPPING_GENERATION_ATTEMPT_FOR_EZ_PAGE_WITH_NO_NAME) {
					// Can't generate the URI because of missing "uniqueness" data
					$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_AUTOGENERATION_EZ_PAGE_HAS_NO_NAME,
						ucwords($languages[$i]['name']));
					
					$messageStack->add_session($failure_message, 'error');
					
					continue;
				}
			} else {
				$uri_mapping = $_POST['uri-mappings'][$languages[$i]['id']];
			}
			
			if (strlen($uri_mapping) > 1) {
				// Make sure URI mapping is relative to root of site and does not have a trailing slash or any
				// illegal characters
				$uri_mapping = $this->_cleanUpURIMapping($uri_mapping);
			}
			
			$insert_uri_mapping = false;
			$update_uri_mapping = false;
			
			if ($uri_mapping != '') {
				// Check if the URI mapping is being updated or does not yet exist
				if ($prev_uri_mapping == '') {
					$insert_uri_mapping = true;
				} else if ($prev_uri_mapping != $uri_mapping) {
					$update_uri_mapping = true;
				}
			}
			
			if ($insert_uri_mapping || $update_uri_mapping) {
				if ($update_uri_mapping) {
					// Consign previous mapping to the history, so old URI mapping isn't broken
					$this->makeURIMappingHistorical($prev_uri_mapping, $languages[$i]['id']);
				}
				
				// Add the new URI mapping
				$uri = $uri_mapping;
				
				$main_page = FILENAME_EZPAGES;
				
				$mapping_added =
					$this->addURIMapping($uri, $languages[$i]['id'], $main_page, null, $page_id);
				
				if ($mapping_added == CEON_URI_MAPPING_ADD_MAPPING_SUCCESS) {
					if ($insert_uri_mapping) {
						$success_message = sprintf(CEON_URI_MAPPING_TEXT_EZ_PAGE_MAPPING_ADDED,
							ucwords($languages[$i]['name']),
							'<a href="' . HTTP_SERVER . $uri . '" target="_blank">' . $uri . '</a>');
					} else {
						$success_message = sprintf(CEON_URI_MAPPING_TEXT_EZ_PAGE_MAPPING_UPDATED,
							ucwords($languages[$i]['name']),
							'<a href="' . HTTP_SERVER . $uri . '" target="_blank">' . $uri . '</a>');
					}
					
					$messageStack->add_session($success_message, 'success');
					
				} else {
					if ($mapping_added == CEON_URI_MAPPING_ADD_MAPPING_ERROR_MAPPING_EXISTS) {
						$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_ADD_MAPPING_EXISTS,
							ucwords($languages[$i]['name']),
							'<a href="' . HTTP_SERVER . $uri . '" target="_blank">' . $uri . '</a>');
						
					} else if ($mapping_added == CEON_URI_MAPPING_ADD_MAPPING_ERROR_DATA_ERROR) {
						$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_ADD_MAPPING_DATA,
							ucwords($languages[$i]['name']), $uri);
					} else {
						$failure_message = sprintf(CEON_URI_MAPPING_TEXT_ERROR_ADD_MAPPING_DB,
							ucwords($languages[$i]['name']), $uri);
					}
					
					$messageStack->add_session($failure_message, 'error');
				}
			} else if ($prev_uri_mapping != '' && $uri_mapping == '') {
				// No URI mapping, consign existing mapping to the history, so old URI mapping isn't broken
				$this->makeURIMappingHistorical($prev_uri_mapping, $languages[$i]['id']);
				
				$success_message = sprintf(CEON_URI_MAPPING_TEXT_EZ_PAGE_MAPPING_MADE_HISTORICAL,
					ucwords($languages[$i]['name']));
				
				$messageStack->add_session($success_message, 'caution');
			}
		}
	}
	
	// }}}
	
	
	// {{{ deleteConfirmHandler()
	
	/**
	 * Handles the Ceon URI Mapping functionality when an EZ-page is being deleted.
	 *
	 * @access  public
	 * @param   integer   $page_id   The ID of the EZ-page.
	 * @return  none
	 */
	public function deleteConfirmHandler($page_id)
	{
		$selections = array(
			'main_page' => FILENAME_EZPAGES,
			'associated_db_id' => (int) $page_id
			);
		
		$this->deleteURIMappings($selections);
	}
	
	// }}}
	
	
	// {{{ configureEnvironment()
	
	/**
	 * Simply configures the Ceon URI Mapping variables when the EZ-Page admin page is being displayed/used.
	 *
	 * @access  public
	 * @return  none
	 */
	public function configureEnvironment()
	{
		// Get any current EZ-Page URI mappings from the database if necessary
		if (isset($_GET['ezID']) && empty($_POST)) {
			$columns_to_retrieve = array(
				'language_id',
				'uri'
				);
			
			$selections = array(
				'main_page' => FILENAME_EZPAGES,
				'associated_db_id' => (int) $_GET['ezID'],
				'current_uri' => 1
				);
			
			$prev_uri_mappings_result = $this->getURIMappingsResultset($columns_to_retrieve, $selections);
			
			while (!$prev_uri_mappings_result->EOF) {
				$this->_prev_uri_mappings[$prev_uri_mappings_result->fields['language_id']] =
					$prev_uri_mappings_result->fields['uri'];
				
				$prev_uri_mappings_result->MoveNext();
			}
			
			$this->_uri_mappings = $this->_prev_uri_mappings;
			
		} else {
			// Use the URI mappings passed in the POST variables
			$this->_prev_uri_mappings = $_POST['prev-uri-mappings'];
			$this->_uri_mappings = $_POST['uri-mappings'];
			
			$this->_uri_mapping_autogen = (isset($_POST['uri-mapping-autogen']) ? true : false);
		}
	}
	
	// }}}
	
	
	// {{{ buildEZPageURIMappingFields()
	
	/**
	 * Builds the input fields for adding/editing URI mappings for EZ-Pages.
	 *
	 * @access  public
	 * @return  string   The source for the input fields.
	 */
	public function buildEZPageURIMappingFields()
	{
		$languages = zen_get_languages();
		
		$num_uri_mappings = count($this->_uri_mappings);
		
		$num_languages = count($languages);
		
		$uri_mapping_input_fields .= '</div><div class="form-group"><label for="uri_mapping" class="col-sm-3 control-label">URI Mapping:</label><div class="col-sm-9 col-md-6">';
		
		for ($i = 0, $n = count($languages); $i < $n; $i++) {
			$uri_mapping_input_fields .= "<div class=\"input-group\">";
			
			if (!isset($this->_prev_uri_mappings[$languages[$i]['id']])) {
				$this->_prev_uri_mappings[$languages[$i]['id']] = '';
			}
			
			if (!isset($this->_uri_mappings[$languages[$i]['id']])) {
				$this->_uri_mappings[$languages[$i]['id']] = '';
			}
			
			$uri_mapping_input_fields .= zen_draw_hidden_field('prev-uri-mappings[' . $languages[$i]['id'] . ']',
				$this->_prev_uri_mappings[$languages[$i]['id']]);
				
				$uri_mapping_input_fields .= "<div class=\"input-group\"><span class=\"input-group-addon\">";
			
			$uri_mapping_input_fields .= zen_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] .
				'/images/' . $languages[$i]['image'], $languages[$i]['name']) . '</span>' .
				zen_draw_input_field('uri-mappings[' . $languages[$i]['id'] . ']',
				$this->_uri_mappings[$languages[$i]['id']], 'size="60" class="form-control"');
			
			$uri_mapping_input_fields .= "</div><br/>";
		}
		
		
		
		if ($this->_autogenEnabled()) {
			if ($num_languages == 1) {
				$autogen_message = CEON_URI_MAPPING_TEXT_EZ_PAGE_URI_AUTOGEN;
			} else {
				$autogen_message = CEON_URI_MAPPING_TEXT_EZ_PAGE_URIS_AUTOGEN;
			}
			
			if ($num_uri_mappings == 0) {
				$autogen_selected = true;
			} else {
				$autogen_selected = false;
				
				if ($num_uri_mappings == 1) {
					$autogen_message .= '<br />' . CEON_URI_MAPPING_TEXT_URI_AUTOGEN_ONE_EXISTING_MAPPING;
				} else if ($num_uri_mappings == $num_languages) {
					$autogen_message .= '<br />' . CEON_URI_MAPPING_TEXT_URI_AUTOGEN_ALL_EXISTING_MAPPINGS;
				} else {
					$autogen_message .= '<br />' . CEON_URI_MAPPING_TEXT_URI_AUTOGEN_SOME_EXISTING_MAPPINGS;
				}
			}
			
			if (!$autogen_selected && $this->_uri_mapping_autogen) {
				$autogen_selected = true;
			}
			
			$uri_mapping_input_fields .=
				zen_draw_checkbox_field('uri-mapping-autogen', '1', $autogen_selected) . ' ' . $autogen_message;
		} else {
			$uri_mapping_input_fields .= CEON_URI_MAPPING_TEXT_URI_AUTOGEN_DISABLED;
		}	

		$uri_mapping_input_fields .= "<br/><br/>";
		return $uri_mapping_input_fields;
	}
	
	/// }}}
}

// }}}
