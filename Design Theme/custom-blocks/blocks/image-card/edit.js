import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button, RangeControl } from '@wordpress/components';
import './editor.scss';

export default function Edit({ attributes, setAttributes }) {
	const { imageUrl, imageAlt, title, description, linkUrl, imgWidth, imgHeight, borderRadius } = attributes;

	const blockProps = useBlockProps({
		className: 'image-card-block',
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Image Settings', 'custom-blocks')}>
					<RangeControl
						label={__('Width (px)', 'custom-blocks')}
						value={imgWidth}
						onChange={(value) => setAttributes({ imgWidth: value })}
						min={100}
						max={600}
					/>
					<RangeControl
						label={__('Height (px)', 'custom-blocks')}
						value={imgHeight}
						onChange={(value) => setAttributes({ imgHeight: value })}
						min={100}
						max={600}
					/>
					<RangeControl
						label={__('Border Radius (px)', 'custom-blocks')}
						value={borderRadius}
						onChange={(value) => setAttributes({ borderRadius: value })}
						min={0}
						max={100}
					/>
					<TextControl
						label={__('Link URL', 'custom-blocks')}
						value={linkUrl}
						onChange={(value) => setAttributes({ linkUrl: value })}
						placeholder="https://example.com"
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<div className="image-container" style={{
					width: `${imgWidth}px`,
					height: `${imgHeight}px`,
					borderRadius: `${borderRadius}px`,
					overflow: 'hidden'
				}}>
					<MediaUpload
						onSelect={(media) =>
							setAttributes({ imageUrl: media.url, imageAlt: media.alt })
						}
						type="image"
						value={imageUrl}
						render={({ open }) => (
							<Button onClick={open} className="image-upload-button">
								{imageUrl ? (
									<img
										src={imageUrl}
										alt={imageAlt}
										style={{ width: '100%', height: '100%', objectFit: 'cover' }}
									/>
								) : (
									__('Upload Image', 'custom-blocks')
								)}
							</Button>
						)}
					/>
				</div>

				<RichText
					tagName="h3"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Add title...', 'custom-blocks')}
				/>

				<RichText
					tagName="p"
					value={description}
					onChange={(value) => setAttributes({ description: value })}
					placeholder={__('Add description...', 'custom-blocks')}
				/>
			</div>
		</>
	);
}
