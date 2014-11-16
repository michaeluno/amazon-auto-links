<?php
/**
 * Handles unit outputs.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		2.0.0
*/
abstract class AmazonAutoLinks_Units_ {

	function __construct( $arrArgs ) {
		
		$this->arrArgs = $arrArgs;
		
	}

	public function render() {
		echo $this->getOutput();
	}
	
	public function getOutput() {
			
		// Retrieve IDs 
		$arrIDs = array();

		// The id parameter.
		if ( isset( $this->arrArgs['id'] ) )	// the id parameter can accept comma delimited ids.
			if ( is_string( $this->arrArgs['id'] ) || is_integer( $this->arrArgs['id'] ) )
				$arrIDs = array_merge( AmazonAutoLinks_Utilities::convertStringToArray( $this->arrArgs['id'], "," ), $arrIDs );
			else if ( is_array( $this->arrArgs['id'] ) )
				$arrIDs = $this->arrArgs['id'];	// The Auto-insert feature passes the id as array.
			
		// The label parameter.
		if ( isset( $this->arrArgs['label'] ) ) {
			
			$this->arrArgs['_labels'] = AmazonAutoLinks_Utilities::convertStringToArray( $this->arrArgs['label'], "," );
			$arrIDs = array_merge( $this->getPostIDsByLabel( $this->arrArgs['_labels'], isset( $arrArgs['operator'] ) ? $arrArgs['operator'] : null ), $arrIDs );
			
		}
			
		$_aOutputs = array();
		$arrIDs = array_unique( $arrIDs );

		foreach( $arrIDs as $_iID ) 
			$_aOutputs[] = $this->getOutputByID( $_iID );
		
		return implode( '', $_aOutputs );

	}
		
		protected function getOutputByID( $iPostID ) {

			$arrUnitOptions = AmazonAutoLinks_Option::getUnitOptionsByPostID( $iPostID );

			/**
			 * The auto-insert sets the 'id' as array storing multiple ids. But this method is called per ID so the array should be discarded.
			 */
			$_aSetArgs = $this->arrArgs;
			unset( $_aSetArgs['id'] );
			
			$arrUnitOptions = $_aSetArgs + $arrUnitOptions + array( 
				'unit_type' => null,
				'id' => $iPostID,
			);	// if the unit gets deleted, auto-insert causes an error for not finding the options
	
			switch ( $arrUnitOptions['unit_type'] ) {
				case 'category':
					$_oAALCat = new AmazonAutoLinks_Unit_Category( $arrUnitOptions );
					return $_oAALCat->getOutput();
				case 'tag':
					$_oAALTag = new AmazonAutoLinks_Unit_Tag( $arrUnitOptions );
					return $_oAALTag->getOutput();
				case 'search':
					$_oAALSearch = new AmazonAutoLinks_Unit_Search( $arrUnitOptions );
					return $_oAALSearch->getOutput();
				case 'item_lookup':
					$_oAALSearch = new AmazonAutoLinks_Unit_Search_ItemLookup( $arrUnitOptions );				
					return $_oAALSearch->getOutput();
				case 'similarity_lookup':
					$_oAALSearch = new AmazonAutoLinks_Unit_Search_SimilarityLookup( $arrUnitOptions );				
					return $_oAALSearch->getOutput();				
				default:
					return "<!-- " . AmazonAutoLinks_Commons::$strPluginName . ': ' . __( 'Could not identify the unit type. Please make sure to update the auto-insert definition if you have deleted the unit.', 'amazon-auto-links' ) . " -->";
			}		
		}
		
		protected function getPostIDsByLabel( $arrLabels, $strOperator ) {
			
			// Retrieve the taxonomy slugs of the given taxonomy names.
			$arrTermSlugs = array();
			foreach( ( array ) $arrLabels as $strTermName ) {
				
				$arrTerm = get_term_by( 'name', $strTermName, AmazonAutoLinks_Commons::TagSlug, ARRAY_A );
				$arrTermSlugs[] = $arrTerm['slug'];
				
			}

			return $this->getPostIDsByTag( $arrTermSlugs, 'slug', $strOperator );
			
		}

		public function getPostIDsByTag( $arrTermSlugs, $strFieldType='slug', $strOperator='AND' ) {

			if ( empty( $arrTermSlugs ) ) return array();
				
			$strFieldType = $this->sanitizeFieldKey( $strFieldType );	// only id or slug 

			$arrPostObjects = get_posts( 
				array(
					'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,	
					'posts_per_page' => -1, // ALL posts
					'tax_query' => array(
						array(
							'taxonomy' => AmazonAutoLinks_Commons::TagSlug,	
							'field' => $strFieldType,	// id or slug
							'terms' => $arrTermSlugs,	// the array of term slugs
							'operator' => $this->sanitizeOperator( $strOperator ),	// 'IN', 'NOT IN', 'AND. If the item is only one, use AND.
						)
					)
				)
			);
			$arrIDs = array();
			foreach( $arrPostObjects as $oPost )
				$arrIDs[] = $oPost->ID;
			return array_unique( $arrIDs );
			
		}
		protected function sanitizeFieldKey( $strField ) {
			switch( strtolower( trim( $strField ) ) ) {
				case 'id':
					return 'id';
				default:
				case 'slug':
					return 'slug';
			}		
		}
		protected function sanitizeOperator( $strOperator ) {
			switch( strtoupper( trim( $strOperator ) ) ) {
				case 'NOT IN':
					return 'NOT IN';
				case 'IN':
					return 'IN';
				default:
				case 'AND':
					return 'AND';
			}
		}		
	
}