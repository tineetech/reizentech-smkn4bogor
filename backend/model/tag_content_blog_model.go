// file: model/tag_content_blog.go
package model

const TableTagContentBlog = "tag_content_blog"

type TagContentBlog struct {
	ContentBlogID string `json:"content_blog_id" db:"content_blog_id"` // FK ke content_blog.id
	TagBlogID     int64  `json:"tag_blog_id"      db:"tag_blog_id"`    // FK ke tag_blog.id
}
