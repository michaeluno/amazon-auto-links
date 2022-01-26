/**
 * WordPress dependencies
 */
import { ComboboxControl, Spinner } from "@wordpress/components";
import { useState, useEffect } from "@wordpress/element";


const postToControlOption = (post) => ({
	label: post.title.raw,
	value: post.id,
});

const ComboboxWrapper = ({ children }) => {
	return <div className="aal-gutenberg-combobox-wrapper">{children}</div>;
};

const PostControl = ({ label, posts, value, onChange }) => {
	const [options, setOptions] = useState([]);

	useEffect(() => {
		if (posts) {
			setOptions(posts.map(postToControlOption));
		}
	}, [posts]);

	if (typeof posts === null) return <Spinner />;

	return (
		<ComboboxWrapper>
			<ComboboxControl
				label={label}
				value={value}
				onChange={onChange}
				options={options}
			/>
		</ComboboxWrapper>
	);
};

export default PostControl;