const $input = $('#send-message-input')
const $button = $('#send-message-button')

$input.on('keydown', (event) => {
	if (event.key === 'Enter') {
		event.preventDefault()
		const value = $input.val()
		if (value === '') return
		ws.send(
			JSON.stringify({
				reason: 'chat',
				data: {
					message: value,
				},
			}),
		)
		$input.val('')
	}
})

const appendMessage = (data) => {
	const messageElement = '<div class="mb-2 p-2 bg-blue-100 rounded-lg"><p>' + data.message + '</p></div>'
	$('#chat-messages-container').append(messageElement)
	$('#chat-messages-container').scrollTop($('#chat-messages-container')[0].scrollHeight)
}

$button.click((event) => {
	event.preventDefault()
	const value = $input.val()
	if (value === '') return
	ws.send(
		JSON.stringify({
			reason: 'chat',
			data: {
				message: value,
			},
		}),
	)
	$input.val('')
	// appendMessage(value)
})
