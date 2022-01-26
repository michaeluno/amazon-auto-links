import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
  PanelBody,
} from '@wordpress/components';
import {
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

import './editor.scss';
import './style.scss';
import { iconAmazon, } from './icons';

/**
 * Internal dependencies
 */
import PostControl from "./control/PostControl";
import { getPosts } from "./function/getPosts";
import { getMaxNumberOfItemsControl } from "./function/getMaxNumberOfItemsControl";

const getUnitSelectControl = ( attributes, setAttributes ) => {
  const { id } = attributes;
  const units = getPosts( 'amazon_auto_links' );
  return (
    <PostControl
      label={ __( 'Unit Name', 'amazon-auto-links' ) }
      value={ id }
      posts={ units }
      onChange={(value) => {
          setAttributes( {
            id: value,
          } );
        }
      }
    />
  );
}
registerBlockType( 'auto-amazon-links/unit', {
  icon: iconAmazon,
  attributes: {
    id: {
      type: 'integer',
      default: 0,
    },
    count: {
      type: 'integer',
    }
  },
	edit( { name, attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		return (
			<div { ...blockProps }>
        <InspectorControls>
          <PanelBody title={ 'Combo Box Setting' }>
            { getUnitSelectControl( attributes, setAttributes ) }
            { getMaxNumberOfItemsControl( attributes, setAttributes ) }
          </PanelBody>
        </InspectorControls>
        { getUnitSelectControl( attributes, setAttributes ) }
        <ServerSideRender
          block={ name }
          attributes={ attributes }
        />
      </div>
		);
	}
} );