/**
 * Custom 3D spectrum ball cursor (desktop / fine pointer only).
 */
(function () {
	'use strict';

	var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
	if (!finePointer.matches) {
		return;
	}

	var root = document.documentElement;
	var cursor = document.getElementById('trost-cursor');
	if (!cursor) {
		return;
	}

	var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var targetX = window.innerWidth / 2;
	var targetY = window.innerHeight / 2;
	var currentX = targetX;
	var currentY = targetY;
	var visible = false;
	var rafId = null;

	root.classList.add('trost-custom-cursor');

	function setVisible(show) {
		visible = show;
		cursor.classList.toggle('trost-cursor--hidden', !show);
	}

	var hoverSelector =
		'a, button, [role="button"], .button, .shows-nav-item, summary, label[for], input[type="submit"], input[type="button"]';

	function onMove(event) {
		targetX = event.clientX;
		targetY = event.clientY;
		if (!visible) {
			setVisible(true);
		}
		var hit = document.elementFromPoint(event.clientX, event.clientY);
		cursor.classList.toggle('trost-cursor--hover', !!(hit && hit.closest(hoverSelector)));
	}

	function tick() {
		var ease = reducedMotion ? 1 : 1;
		currentX += (targetX - currentX) * ease;
		currentY += (targetY - currentY) * ease;
		cursor.style.transform =
			'translate3d(' + currentX + 'px, ' + currentY + 'px, 0) translate(-50%, -50%)';
		rafId = window.requestAnimationFrame(tick);
	}

	document.addEventListener('mousemove', onMove, { passive: true });
	document.addEventListener('mouseleave', function () {
		setVisible(false);
	});
	document.addEventListener('mouseenter', function () {
		if (typeof targetX === 'number') {
			setVisible(true);
		}
	});

	finePointer.addEventListener('change', function (event) {
		if (!event.matches) {
			root.classList.remove('trost-custom-cursor');
			cursor.remove();
			if (rafId) {
				window.cancelAnimationFrame(rafId);
			}
		}
	});

	setVisible(false);
	rafId = window.requestAnimationFrame(tick);
})();
