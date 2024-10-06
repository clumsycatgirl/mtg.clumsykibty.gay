const wsPort = 9009
const ws = new WebSocket(`ws://mtg-ws.clumsykibty.gay/ws`)

ws.onopen = (...params) => {
	console.log('connected to ws server')
	console.log(...params)
}

ws.onerror = console.error

ws.onmessage = (event) => {
	const data = JSON.parse(event.data)

	switch (data.reason) {
		case 'chat':
			appendMessage(data.data)
			break
		case 'health':
			updateOpponentHealth(data.data)
			break
		case 'move':
			move(data)
			break
		case 'draw':
			draw(data)
			break
		case 'get-counter':
			$('#hand').children().last().attr('id', data.data.counter)
			ws.send(
				JSON.stringify({
					reason: 'draw',
					element: getDataToSendFromElement($('#hand').children().last()[0]),
				}),
			)
			waitingForCounter = false
			break
		case 'tap':
			$(`#${data.data.id}`).css('transform', `rotate(${data.data.tapped ? '-90' : '180'}deg)`)
			break
		case 'text-bubble':
			const $bubble = addTextBubble($(`#${data.data.id} img`), data.data.text)
			$bubble.css('transform', 'rotate(180deg)')
			break
		default:
			break
	}
}

const move = (data) => {
	const $el = $(`#${data.data.id}`)
	const $dataElement = $el.find('#data')
	console.log('moving ', data)
	switch (data.data.where) {
		case 'board':
			if ($el.parent().attr('id') !== 'dragging') {
				$el.detach()
				$('#dragging').append($el)
				$el.find('img').attr('src', data.card.src)
			}
			$el.css({
				position: 'absolute',
				top: 'unset',
				left: 'unset',
				bottom: data.data.data.y + '%',
				right: data.data.data.x + '%',
			})
			if ($el.css('transform') !== 'rotate(180deg)' || $el.css('transform') !== 'rotate(-90deg)') {
				$el.css('transform', 'rotate(180deg)')
			}
			$dataElement
				.attr('data-uuid', data.card.uuid)
				.attr('data-multiverse-id', data.card.multiverseId)
				.attr('data-name', data.card.name)
				.attr('data-mana-cost', data.card.manaCost)
				.attr('data-power', data.card.power)
				.attr('data-toughness', data.card.toughness)
				.attr('data-text', data.card.text)
			$el.on('mouseover', function () {
				$(`#show-card-image`).attr('src', data.card.src)
				$(`#show-card-name`).html(data.card.name)
				$(`#show-card-cost`).html(data.card.manaCost)
				$(`#show-card-pt`).html(`${data.card.power}/${data.card.toughness}`)
				$(`#show-card-text`).html(data.card.text)
			})
			break
		case 'hand':
			$.each($dataElement.data(), function (i, v) {
				$dataElement.removeAttr('data-' + i)
			})
			$el.css({
				position: 'relative',
				bottom: '0px',
				right: '0px',
				transform: 'none',
			})
			$el.find('img').attr('src', data.card['back-src'])
			$el.off('mouseover')
			$('#opponent-hand').append($el)
			break
		case 'deck':
			console.log('sending ', $el, ' to deck')
			$el.remove()
			break
		case 'graveyard':
			$el.detach()
			$el.css({
				'position': 'relative',
				'top': 0,
				'left': 0,
				'margin-top': '0',
				'transform': 'none',
			})
			$('#opponent-graveyard-content').append($el)
			break
	}
}

const draw = (data) => {
	const $newElement = $(`
        <div id="${data.element.id}" class="relative card">
          <div class="absolute inset-0 w-full h-full bg-blue-500 gradient-loading loading-placeholder"></div>
          <img class="absolute inset-0 w-full h-full"
               src="/assets/images/back.webp"
               onload="showImage(this)"
               data-card="true">
          <div id="data"></div>
        </div>
      `)

	$('#opponent-hand').append($newElement)
}

const getDataToSendFromElement = (element) => {
	return {
		id: element.id,
		data: $(element).find('#data')[0].dataset,
		src: $(element).find('img')[0].src,
	}
}

const sendMessage = (message) => {
	ws.send(
		JSON.stringify({
			reason: 'chat',
			data: {
				message: message,
			},
		}),
	)
}
