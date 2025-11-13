import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { imageUrl, imageAlt, title, description, linkUrl, imgWidth, imgHeight, borderRadius } = attributes;

	const blockProps = useBlockProps.save({
		className: 'image-card-block',
	});

	const imageElement = (
		<img
			src={imageUrl}
			alt={imageAlt}
			style={{
				width: `${imgWidth}px`,
				height: `${imgHeight}px`,
				borderRadius: `${borderRadius}px`,
				objectFit: 'cover'
			}}
		/>
	);

	return (
		<div {...blockProps}>
			{linkUrl ? (
				<a href={linkUrl} target="_blank" rel="noopener noreferrer">{imageElement}</a>
			) : (
				imageElement
			)}

			<RichText.Content tagName="h3" value={title} />
			<RichText.Content tagName="p" value={description} />
		</div>
	);
}
