let isDragging = false
const setupCards = () => {
	let zIndexCounter = 0
	let currentMovable = null
	let offsetX = 0
	let offsetY = 0

	const startDrag = (movable, event) => {
		if (event.shiftKey) return
		if ($(movable).find('#data').data('gy') === 'true') return
		const rect = movable.getBoundingClientRect()
		offsetX = event.pageX - rect.x
		offsetY = event.pageY - rect.y

		$(movable).css('z-index', ++zIndexCounter)
		movable.style.cursor = 'grabbing'
		if ($(movable).parent().attr('id') === 'hand') {
			$(movable).css({
				'position': 'absolute',
				'top': rect.top,
				'left': rect.left,
				'margin-top': '0',
			})
		}
		$('#dragging').append(movable)
		isDragging = true
		currentMovable = movable

		$(document).on('mousemove', handleMouseMove)
		$(document).on('mouseup', stopDrag)
	}

	const stopDrag = async () => {
		if (!currentMovable) return

		currentMovable.style.cursor = 'default'

		const failSafe = !isHoveringOverDeck && !isHoveringOverHand

		if (isHoveringOverHand) {
			const $hand = $('#hand')
			$hand.append(currentMovable)
			if ($hand.hasClass('bg-red-500')) {
				$hand.removeClass('bg-red-500')
				isHoveringOverHand = false
			}
			$(currentMovable).css({
				position: 'relative',
				top: '0px',
				left: '0px',
			})

			ws.send(
				JSON.stringify({
					reason: 'move',
					data: {
						id: $(currentMovable).attr('id'),
						where: 'hand',
					},
				}),
			)
		}

		const tempCurrentMovable = currentMovable
		if (isHoveringOverDeck) {
			const id = $(tempCurrentMovable).attr('id')
			const uuid = $(tempCurrentMovable).find('#data').data('card-uuid')

			const result = await Swal.fire({
				title: 'where do you want to put the card?',
				showDenyButton: true,
				showCancelButton: true,
				confirmButtonText: 'Top',
				denyButtonText: 'Bottom',
				cancelButtonText: 'Cancel',
			})

			let sent = false
			if (await result.isConfirmed) {
				// top
				await fetch(`deck/to/top/${uuid}`)
				$(tempCurrentMovable).remove()
				sent = true
			} else if (await result.isDenied) {
				// bottom
				await fetch(`deck/to/bottom/${uuid}`)
				$(tempCurrentMovable).remove()
				sent = true
			} else if (await result.isDismissed) {
				// cancel
			}
			$('#deck-overlay').addClass('hidden')

			if (sent) {
				ws.send(
					JSON.stringify({
						reason: 'move',
						data: {
							id: id,
							where: 'deck',
						},
					}),
				)
				sendMessage(`[move to deck] ${$(tempCurrentMovable).find('#data').data('name')}`)
			}
		}

		if (failSafe) {
			setTimeout(() => {
				$('#dragging').append(tempCurrentMovable)
			}, 1)
		}

		isDragging = false
		currentMovable = null
		$(document).off('mousemove', handleMouseMove)
	}

	const handleMouseMove = (e) => {
		if (isDragging && currentMovable) {
			const mouseX = e.pageX - offsetX
			const mouseY = e.pageY - offsetY

			$(currentMovable).css({
				position: 'absolute',
				top: mouseY + 'px',
				left: mouseX + 'px',
			})

			ws.send(
				JSON.stringify({
					reason: 'move',
					data: {
						id: $(currentMovable).attr('id'),
						where: 'board',
						data: { x: (mouseX / $(document).width()) * 100, y: (mouseY / $(document).height()) * 100 },
					},
				}),
			)
		}
	}

	const tap = (movable, event) => {
		if (event.shiftKey) return
		movable.tapped = !movable.tapped
		$(movable).css('transform', `rotate(${movable.tapped ? '-90' : '0'}deg)`)

		ws.send(
			JSON.stringify({
				reason: 'tap',
				data: {
					id: $(movable).attr('id'),
					tapped: movable.tapped,
				},
			}),
		)
		sendMessage(`[tap] ${$(movable).find('#data').data('name')}`)
	}

	$('.movable')
		.off('mousedown')
		.on('mousedown', function mousedown(e) {
			e.preventDefault()
			$(this).css('z-index', ++zIndexCounter)

			switch (e.which) {
				case 1: // left mouse button
					startDrag(this, e)
					$(document).on('mousemove', handleMouseMove)
					$(document).on('mouseup', stopDrag)
					break
				case 2: // middle mouse button
					break
				case 3: // right mouse button
					tap(this, e)
					break
				default:
					alert('weird mouse')
					break
			}
		})
		.off('mouseover')
		.on('mouseover', function mouseover() {
			const $data = $(this).find('#data')
			const multiverseId = $data.data('multiverse-id')
			const name = $data.data('name')
			const manaCost = $data.data('mana-cost')
			const power = $data.data('power')
			const toughness = $data.data('toughness')
			const text = $data.data('text')

			$(`#show-card-image`).attr(
				'src',
				`https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid=${multiverseId}&type=card`,
			)
			$(`#show-card-name`).html(name)
			$(`#show-card-cost`).html(manaCost)
			$(`#show-card-pt`).html(`${power}/${toughness}`)
			$(`#show-card-text`).html(text)
		})

	$(document).off('mouseup', stopDrag).on('mouseup', stopDrag)
}

$(setupCards)

const uploadDeck = () => {
	const fileInput = document.getElementById('deck-upload')
	const file = fileInput?.files[0]

	if (!file) {
		alert('Please select a file.')
		return
	}

	const formData = new FormData()
	formData.append('deck', file)

	fetch('/readdeck', {
		method: 'POST',
		body: formData,
	}).catch(console.error)
}

let waitingForCounter = false
const drawFromDeck = async () => {
	if (waitingForCounter) return
	try {
		waitingForCounter = true
		const response = await fetch('/draw')
		const data = await response.text()
		$('#hand').append(data)
		const $newCard = $('#hand').children().last()
		$newCard.css({
			position: 'relative',
			top: '0px',
			left: '0px',
		})
		ws.send(
			JSON.stringify({
				reason: 'get-counter',
				data: $newCard.find('#data')[0].dataset,
			}),
		)
		setupCards()
	} catch (error) {
		console.error('Error:', error)
	}
}

const drawToken = async () => {
	try {
		const response = await fetch('/token')
		const data = await response.text()
		$('#hand').append(data)
		$('#hand').children().last().css({
			position: 'relative',
			top: '0px',
			left: '0px',
		})
		setupCards()
		ws.send(
			JSON.stringify({
				reason: 'get-counter',
			}),
		)
	} catch (error) {
		console.error('Error:', error)
	}
}

const increaseLife = () => {
	fetch('/life/increase')
		.then((response) => response.text())
		.then((html) => $('#life').replaceWith(html))
		.then(() =>
			ws.send(
				JSON.stringify({
					reason: 'health',
					data: $('#life-text').text(),
				}),
			),
		)
		.then(() => sendMessage(`[update health] ${$('#life-text').text()}`))
		.catch((error) => console.error('Error:', error))
}

const decreaseLife = () => {
	fetch('/life/decrease')
		.then((response) => response.text())
		.then((html) => $('#life').replaceWith(html))
		.then(() =>
			ws.send(
				JSON.stringify({
					reason: 'health',
					data: $('#life-text').text(),
				}),
			),
		)
		.then(() => sendMessage(`[update health] ${$('#life-text').text()}`))
		.catch((error) => console.error('Error:', error))
}

const updateOpponentHealth = (health) => $('#opp-life-text').text(health)

let isHoveringOverHand = false
$(document).on('mousemove', (event) => {
	if (!isDragging) return

	const $hand = $('#hand')
	const handOffset = $hand.offset()
	const handWidth = $hand.outerWidth()
	const handHeight = $hand.outerHeight()

	if (
		event.pageX >= handOffset.left &&
		event.pageX <= handOffset.left + handWidth &&
		event.pageY >= handOffset.top &&
		event.pageY <= handOffset.top + handHeight
	) {
		if (!$hand.hasClass('bg-red-500')) $hand.addClass('bg-red-500')
		isHoveringOverHand = true
	} else {
		if ($hand.hasClass('bg-red-500')) $hand.removeClass('bg-red-500')
		isHoveringOverHand = false
	}
})

let isHoveringOverDeck = false
$(document).on('mousemove', (event) => {
	if (!isDragging) return

	const $deck = $('#deck')
	const deckOffset = $deck.offset()
	const deckWidth = $deck.outerWidth()
	const deckHeight = $deck.outerHeight()

	const cssClass = 'hidden'
	const $overlay = $('#deck-overlay')

	if (
		event.pageX >= deckOffset.left &&
		event.pageX <= deckOffset.left + deckWidth &&
		event.pageY >= deckOffset.top &&
		event.pageY <= deckOffset.top + deckHeight
	) {
		if ($overlay.hasClass(cssClass)) {
			$overlay.removeClass(cssClass)
			isHoveringOverDeck = true
		}
	} else {
		$overlay.addClass(cssClass)
		isHoveringOverDeck = false
	}
})

const addTextBubble = (target, text) => {
	const bubble = document.createElement('div')
	bubble.className = 'absolute bottom-0 left-0 mb-2 ml-2 bg-white text-black text-xs px-2 py-1 rounded shadow'
	bubble.innerText = text

	$(target).parent().append(bubble)
	// target.appendChild(bubble)

	$(bubble).on('mousedown', function (e) {
		if (!e.shiftKey) return
		e.preventDefault()
		$(this).remove()
	})

	return $(bubble)
}

const addTextBubbleFromInput = () => {
	$(document).on('click', function click(event) {
		if (!$(event.target).data('card')) return
		console.log(event.target)

		const text = $('#popup-text').val()
		addTextBubble(event.target, text)
		$('#popup-text').val('')

		$(document).off('click', click)

		ws.send(
			JSON.stringify({
				reason: 'text-bubble',
				data: {
					id: $(event.target).parent().attr('id'),
					text: text,
				},
			}),
		)
		sendMessage(`[added text bubble] ${text} to ${$(event.target).parent().find('#data').data('name')}`)
	})
}

let searchOverlayShown = false
const closeOverlay = () => {
	$('#search-overlay').addClass('hidden')
	searchOverlayShown = false
	$('#graveyard').addClass('hidden')
	$('#opponent-graveyard').addClass('hidden')
	graveyardOverlayShown = false
}
const showSearch = () => {
	$('#search-overlay').removeClass('hidden')
	searchOverlayShown = true
}

$(document).on('keydown', (event) => {
	if (event.key === 'Escape' && (searchOverlayShown || graveyardOverlayShown)) {
		closeOverlay()
	}
})

const getCardFromDeckSearch = (element) => {
	$('#hand').append($(element).parent())
	element.onclick = () => {}
	setupCards()
	closeOverlay()

	const formData = new FormData()
	formData.append('uuid', $(element).parent().find('#data').data('card-uuid'))
	fetch(`/remove`, {
		method: 'POST',
		body: formData,
	})
}

let graveyardOverlayShown = false
$('#open-gy-btn').on('click', () => {
	$('#graveyard').removeClass('hidden')
	graveyardOverlayShown = true
})
$('#open-opp-gy-btn').on('click', () => {
	$('#opponent-graveyard').removeClass('hidden')
	graveyardOverlayShown = true
})

$('#send-to-gy-btn').on('click', () => {
	$(document).on('click', function click(event) {
		if (!$(event.target).data('card')) return

		const div = $(event.target).parent()
		$(div).find('#data').data('gy', 'true')

		$(div).on('click', function divClick(event) {
			if (!event.shiftKey) return

			div.onclick = () => {}
			$(div)
				.css({
					'position': 'relative',
					'top': 0,
					'left': 0,
					'margin-top': '0',
					'transform': 'none',
				})
				.on('click', () => {
					console.log('is hand')
					$('#hand').append(div)
					$(div).find('#data').data('gy', 'false')

					$(div).off('click')
					div.onclick = () => {}

					setupCards()
					closeOverlay()

					ws.send(
						JSON.stringify({
							reason: 'move',
							data: {
								id: div.attr('id'),
								where: 'hand',
							},
						}),
					)
					sendMessage(`[move to hand from graveyard] ${div.find('#data').data('name')}`)
				})
			$('#graveyard-content').append(div)

			ws.send(
				JSON.stringify({
					reason: 'move',
					data: {
						id: div.attr('id'),
						where: 'graveyard',
					},
				}),
			)
			sendMessage(`[move to graveyard] ${div.find('#data').data('name')}`)
		})

		$(document).off('click', click)
	})
})

const sendShuffleMessage = () => {
	ws.send(
		JSON.stringify({
			reason: 'chat',
			data: {
				message: 'shuffled deck',
			},
		}),
	)
}
    