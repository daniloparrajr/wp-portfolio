import { addFilter } from '@wordpress/hooks';

import { createHigherOrderComponent } from "@wordpress/compose";

import { InspectorControls } from '@wordpress/block-editor';

import { PanelBody, PanelRow, ToggleControl } from "@wordpress/components";

import { __ } from "@wordpress/i18n";

addFilter(
  "blocks.registerBlockType",
  "portfolio/additional-button-settings-attributes",
  (settings, name) => {
    if ( name !== 'core/button' ) {
      return settings;
    }

    return {
      ...settings,
      attributes: {
        ...settings.attributes,
        isCircular: {
          type: "boolean",
          default: false,
        }
      }
    };
  });

function Edit(props) {
  return (
    <InspectorControls>
      <PanelBody>
        <PanelRow>
          <ToggleControl
            __nextHasNoMarginBottom
            checked={props.attributes.isCircular}
            label={__("Circular", "portfolio")}
            help={__("Make the button circular regardless of content.")}
            onChange={() => props.setAttributes({ isCircular: !props.attributes.isCircular })}
          />
        </PanelRow>
      </PanelBody>
    </InspectorControls>
  );
}

addFilter(
  "editor.BlockEdit",
  "portfolio/additional-button-settings",
  createHigherOrderComponent((BlockEdit) => {
    return (props) => {
      if ( 'core/button' !== props.name ) {
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

// Add custom classes in the editor (dynamic rendering for the block editor view)
addFilter(
  'editor.BlockListBlock',
  'portfolio/apply-circular-classname-in-button-wrapper',
  (BlockListBlock) => (props) => {
    if (props.name !== 'core/button') {
      return <BlockListBlock {...props} />;
    }

    const { isCircular } = props.attributes;

    let newClassNames = `${props.className || ''}`.trim();

    if (isCircular) {
      newClassNames += ' is-circular';
    }

    const newProps = {
      ...props,
      className: newClassNames,
    };

    return <BlockListBlock {...newProps} />;
  }
);