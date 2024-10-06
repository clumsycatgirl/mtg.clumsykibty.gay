package app.scripts;

import js.html.DivElement;
import js.html.Element;
import js.html.MouseEvent;
import haxe.ds.Option;
import js.Browser;
import js.html.ButtonElement;

class Main {
	public static function main() {
		final button:ButtonElement = cast(Browser.document.querySelector("#meow-btn"));
		if (button != null) {
			button.onclick = function(event:MouseEvent) {
				trace("click");
			};
		}

		final button2 = switch Html.trySelect("#meow-btn", ButtonElement) {
			case Some(value): value;
			case None: throw "Element not found";
		};
		trace(button2);

		final button3 = Html.select("#meow-btn", ButtonElement);
		trace(button3);

		final divs = Html.selectAll("div", DivElement);
	}
}

@:nullSafety
class Html {
	@:generic
	public static function select<T:Element>(selector:String, cls:Null<Class<T>> = null):T {
		var element = Browser.document.querySelector(selector);

		if (element == null) {
			throw "Element not found";
		}

		if (cls != null && !Std.isOfType(element, cls)) {
			throw "Element is not of the expected type";
		}

		return cast element;
	}

	@:generic
	public static function trySelect<T:Element>(selector:String, cls:Null<Class<T>> = null):Option<T> {
		try {
			return Some(select(selector, cls));
		} catch (e:Dynamic) {
			return None;
		}
	}

	@:generic
	public static function selectAll<T:Element>(selector:String, cls:Null<Class<T>> = null):Array<T> {
		var elements = Browser.document.querySelectorAll(selector);

		if (elements == null) {
			throw "Elements not found";
		}

		if (cls != null && !Std.isOfType(elements, cls)) {
			throw "Elements are not of the expected type";
		}

		return cast elements;
	}

	@:generic
	public static function trySelectAll<T:Element>(selector:String, cls:Null<Class<T>> = null):Option<Array<T>> {
		try {
			return Some(selectAll(selector, cls));
		} catch (e:Dynamic) {
			return None;
		}
	}
}
