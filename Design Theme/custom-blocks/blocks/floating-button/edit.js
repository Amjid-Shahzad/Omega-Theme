import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	MediaUpload,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	RangeControl,
	SelectControl,
	Button,
	ColorPicker,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';

const Edit = ({ attributes, setAttributes }) => {
	const {
		enabled,
		text,
		url,
		borderRadius,
		borderUnit,
		width,
		height,
		bgColor,
		hoverBgColor,
		textColor,
		hoverTextColor,
		imageUrl,
		position,
		margin,
		animation,
	} = attributes;

	if (!enabled) {
		return (
			<div className="floating-button-disabled">
				<Button
					variant="secondary"
					onClick={() => setAttributes({ enabled: true })}
				>
					{__('Enable Floating Button', 'custom-blocks')}
				</Button>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				{/* 🧩 Button Settings */}
				<PanelBody title={__('Button Settings', 'custom-blocks')} initialOpen={true}>
					<ToggleControl
						label={__('Enable Button', 'custom-blocks')}
						checked={enabled}
						onChange={(value) => setAttributes({ enabled: value })}
					/>
					<TextControl
						label={__('Button URL', 'custom-blocks')}
						value={url}
						onChange={(value) => setAttributes({ url: value })}
					/>
					<UnitControl
						label={__('Border Radius', 'custom-blocks')}
						value={`${borderRadius}${borderUnit}`}
						units={[
							{ value: 'px', label: 'px' },
							{ value: '%', label: '%' },
						]}
						onChange={(value) => {
							const num = parseFloat(value);
							const unit = value.replace(/[0-9.]/g, '');
							setAttributes({
								borderRadius: num,
								borderUnit: unit || 'px',
							});
						}}
					/>
					<RangeControl
						label={__('Width (px)', 'custom-blocks')}
						value={width}
						onChange={(value) => setAttributes({ width: value })}
						min={30}
						max={300}
					/>
					<RangeControl
						label={__('Height (px)', 'custom-blocks')}
						value={height}
						onChange={(value) => setAttributes({ height: value })}
						min={30}
						max={300}
					/>
					<p><strong>{__('Text Color', 'custom-blocks')}</strong></p>
					<ColorPicker
						color={textColor}
						onChangeComplete={(value) =>
							setAttributes({ textColor: value.hex })
						}
						disableAlpha
					/>
					<p><strong>{__('Background Color', 'custom-blocks')}</strong></p>
					<ColorPicker
						color={bgColor}
						onChangeComplete={(value) =>
							setAttributes({ bgColor: value.hex })
						}
						disableAlpha
					/>
					<MediaUpload
						onSelect={(media) =>
							setAttributes({ imageUrl: media.url })
						}
						render={({ open }) => (
							<Button onClick={open} variant="secondary">
								{imageUrl
									? __('Change Icon/Image', 'custom-blocks')
									: __('Upload Icon/Image', 'custom-blocks')}
							</Button>
						)}
					/>
					<Button
						isDestructive
						variant="link"
						onClick={() => setAttributes({ imageUrl: '' })}
					>
						{__('Remove Image', 'custom-blocks')}
					</Button>
				</PanelBody>

				{/* 🎨 Hover & Advanced */}
				<PanelBody title={__('Hover & Advanced', 'custom-blocks')} initialOpen={false}>
					<p><strong>{__('Hover Background Color', 'custom-blocks')}</strong></p>
					<ColorPicker
						color={hoverBgColor}
						onChangeComplete={(value) =>
							setAttributes({ hoverBgColor: value.hex })
						}
						disableAlpha
					/>
					<p><strong>{__('Hover Text Color', 'custom-blocks')}</strong></p>
					<ColorPicker
						color={hoverTextColor}
						onChangeComplete={(value) =>
							setAttributes({ hoverTextColor: value.hex })
						}
						disableAlpha
					/>
					<SelectControl
						label={__('Button Position', 'custom-blocks')}
						value={position}
						options={[
							{ label: 'Bottom Right', value: 'bottom-right' },
							{ label: 'Bottom Left', value: 'bottom-left' },
							{ label: 'Top Right', value: 'top-right' },
							{ label: 'Top Left', value: 'top-left' },
						]}
						onChange={(value) => setAttributes({ position: value })}
					/>
					<RangeControl
						label={__('Margin from edges (px)', 'custom-blocks')}
						value={margin}
						onChange={(value) => setAttributes({ margin: value })}
						min={0}
						max={100}
					/>
					<ToggleControl
						label={__('Enable Animation', 'custom-blocks')}
						checked={animation}
						onChange={(value) => setAttributes({ animation: value })}
					/>
				</PanelBody>
			</InspectorControls>

			{/* 🖼️ Preview in Editor */}
			<div
				className={`floating-button ${position} ${animation ? 'animated' : ''}`}
				style={{
					borderRadius: `${borderRadius}${borderUnit}`,
					backgroundColor: bgColor,
					color: textColor,
					width: `${width}px`,
					height: `${height}px`,
					margin: `${margin}px`,
				}}
			>
				<a href={url} style={{ color: textColor }}>
					{imageUrl ? (
						<img src={imageUrl} alt="icon" className="button-icon" />
					) : (
						<RichText
							tagName="span"
							value={text}
							onChange={(value) => setAttributes({ text: value })}
							placeholder={__('Button text…', 'custom-blocks')}
						/>
					)}
				</a>
			</div>
		</>
	);
};

export default Edit;
