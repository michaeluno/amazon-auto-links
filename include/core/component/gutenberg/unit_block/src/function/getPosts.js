/**
 * WordPress dependencies
 */
import { useSelect } from "@wordpress/data";

export function getPosts( postType ) {
	const posts = useSelect(
		(select) =>
			// for arguments, @see https://developer.wordpress.org/rest-api/reference/posts/#arguments
			select("core").getEntityRecords("postType", postType, {
				per_page: -1,
				orderby: "date", // e.g. "title",
				order: "desc", //	e.g. "asc",
				status: "publish",
			}),
		[postType]
	);
	return posts;
}