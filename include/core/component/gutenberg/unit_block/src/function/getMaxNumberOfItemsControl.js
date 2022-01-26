/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export function getMaxNumberOfItemsControl( attributes, setAttributes ) {

  const { count } = attributes;
  return (
    <TextControl
			className="aal-gutenberg-number-control"
      label={ __( 'Maximum Number of Items', 'amazon-auto-links' ) }
      type="number"
      value={ count }
      min="1"
      max={ aalGutenbergUnitBlock.maxNumberOfItems }
      step="1"
      onChange={ ( value ) => {
          setAttributes( {
            count: parseInt( value ),
          } );
        }
      }
    />
  );
}