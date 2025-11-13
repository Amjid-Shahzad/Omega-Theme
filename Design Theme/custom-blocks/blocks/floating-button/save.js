import { RichText } from '@wordpress/block-editor';

const save = ({ attributes }) => {
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

	if (!enabled) return null;

	return (
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
			<a href={url}>
				{imageUrl ? (
					<img src={imageUrl} alt="icon" className="button-icon" />
				) : (
					<RichText.Content tagName="span" value={text} />
				)}
			</a>
			<style>
				{`
					.floating-button:hover {
						background-color: ${hoverBgColor};
						color: ${hoverTextColor};
					}
				`}
			</style>
		</div>
	);
};

export default save;
