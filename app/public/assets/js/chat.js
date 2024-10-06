const $input = $('#send-message-input')
const $button = $('#send-message-button')

const appendMessage = (data) => {
	const messageElement = '<div class="mb-2 p-2 bg-blue-100 rounded-lg"><p>' + data.message + '</p></div>'
	$('#chat-messages-container').append(messageElement)
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
