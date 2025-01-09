import { addFilter } from '@wordpress/hooks';

import { createHigherOrderComponent } from "@wordpress/compose";

import { InspectorControls } from '@wordpress/block-editor';

import { PanelBody, PanelRow, ToggleControl } from "@wordpress/components";

import { __ } from "@wordpress/i18n";

import { useSelect } from "@wordpress/data";

addFilter(
  "blocks.registerBlockType",
  "portfolio/additional-post-template-settings-attributes",
  (settings, name) => {
    if ( name !== 'core/post-template' ) {
      return settings;
    }

    return {
      ...settings,
      attributes: {
        ...settings.attributes,
        areColumnsAlternating: {
          type: "boolean",
          default: false,
        }
      }
    };
  });

function Edit(props) {
  return (
    <InspectorControls>
      <PanelBody title={__("Additional settings")}>
        <PanelRow>
          <ToggleControl
            __nextHasNoMarginBottom
            checked={props.attributes.areColumnsAlternating}
            label={__("Columns alternating", "portfolio")}
            help={__("Make columns alternating.")}
            onChange={() => props.setAttributes({ areColumnsAlternating: !props.attributes.areColumnsAlternating })}
          />
        </PanelRow>
      </PanelBody>
    </InspectorControls>
  );
}

addFilter(
  "editor.BlockEdit",
  "portfolio/additional-post-template-settings",
  createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      if ( 'core/post-template' !== props.name ) {
        return <BlockEdit {...props} />;
      }

      const hasColumnsBlock = useSelect((select)=> {
        const { getBlocks } = select("core/block-editor");
        const childBlocks = getBlocks(props.clientId);
        return childBlocks.some((block) => block.name === "core/columns");
      });

      if ( ! hasColumnsBlock ) {
        return <BlockEdit {...props} />;
      }

      return (
        <>
          <BlockEdit {...props} />
          <Edit {...props} />
        </>
      );
    }
  })
);