/**
 * WordPres dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
  PanelBody,
  Placeholder
} from '@wordpress/components';
import {
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Internal dependencies
 */
import './editor.scss';
import './style.scss';
import { iconAmazon, } from './icons';
import json from '../block.json';
const { name } = json;
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
            id: parseInt( value ) || 0,
          } );
        }
      }
    />
  );
}
const emptyResponsePlaceholder = () => (
  <Placeholder>
    <p>
      { __( 'Select a unit.', 'amazon-auto-links' ) }
    </p>
  </Placeholder>
);
registerBlockType( name, {
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
          EmptyResponsePlaceholder={ emptyResponsePlaceholder }
        />
      </div>
		);
	}
} );