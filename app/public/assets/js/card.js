const showImage = (img) => {
	$(img).parent().find('.loading-placeholder').addClass('hidden')
	img.classList.remove('hidden')
}
