/**
 * Forum Permission Matrix — ACP matrix
 *
 * - colours each cell according to its value, highlights changed cells
 * - counts pending changes and shows the save bar
 * - before saving, shows a recap (group / forum / permission / before → after)
 *   and, for groups driven by a role, asks: update the role or convert it
 * - only changed cells are sent (avoids PHP's max_input_vars limit)
 *
 * @package     verturin/permmatrix
 * @copyright   (c) 2026 verturin
 * @license     GPL-2.0-only
 */
(function () {
	'use strict';

	function each(list, fn) {
		Array.prototype.forEach.call(list, fn);
	}

	function ready(fn) {
		if (document.readyState !== 'loading') {
			fn();
		} else {
			document.addEventListener('DOMContentLoaded', fn);
		}
	}

	ready(function () {
		// Group / type selectors on the Forums tab: reload on change.
		each(document.querySelectorAll('[data-pm-autosubmit]'), function (select) {
			select.addEventListener('change', function () {
				select.form.submit();
			});
		});

		// Permission filter (global tabs).
		var filter = document.getElementById('permmatrix-filter');
		if (filter) {
			filter.addEventListener('input', function () {
				var q = filter.value.trim().toLowerCase();
				each(document.querySelectorAll('.permmatrix-row'), function (row) {
					var text = row.getAttribute('data-pm-search') || '';
					row.hidden = q !== '' && text.indexOf(q) === -1;
				});
			});
		}

		// Backup page: preview of the permissions of the selected extension.
		each(document.querySelectorAll('[data-pm-preview]'), function (select) {
			var preview = document.getElementById(select.getAttribute('data-pm-preview'));
			select.addEventListener('change', function () {
				var opt = select.options[select.selectedIndex];
				var perms = opt ? opt.getAttribute('data-perms') : '';
				preview.textContent = perms || '—';
				preview.classList.toggle('permmatrix-filled', !!perms);
			});
		});

		var form = document.getElementById('permmatrix-form');
		if (!form) {
			return;
		}

		var selects = form.querySelectorAll('select[data-pm-cell]');
		var bar = document.getElementById('permmatrix-bar');
		var counter = document.getElementById('permmatrix-count');
		var dialog = document.getElementById('permmatrix-dialog');
		var recapRows = document.getElementById('permmatrix-recap-rows');
		var rolesBox = document.getElementById('permmatrix-roles');
		var isForums = !!form.querySelector('.permmatrix-table-forums');
		var submitting = false;

		var labels = {
			'1': form.getAttribute('data-lbl-yes'),
			'-1': form.getAttribute('data-lbl-no'),
			'0': form.getAttribute('data-lbl-never')
		};

		function setValueClass(el, value) {
			el.className = el.className.replace(/\bpermmatrix-v-?\d+\b/g, '').trim() + ' permmatrix-v' + value;
		}

		function paint(select) {
			var changed = select.value !== select.getAttribute('data-orig');
			setValueClass(select, select.value);
			select.parentNode.classList.toggle('permmatrix-changed', changed);
		}

		function changedSelects() {
			return Array.prototype.filter.call(selects, function (s) {
				return !s.disabled && s.value !== s.getAttribute('data-orig');
			});
		}

		function refresh() {
			var n = changedSelects().length;
			counter.textContent = String(n);
			bar.classList.toggle('permmatrix-bar-active', n > 0);
		}

		each(selects, function (select) {
			select.addEventListener('change', function () {
				paint(select);
				refresh();
			});
		});

		document.getElementById('permmatrix-undo').addEventListener('click', function () {
			each(selects, function (select) {
				select.value = select.getAttribute('data-orig');
				paint(select);
			});
			refresh();
		});

		function cell(tr, text, cls) {
			var td = document.createElement('td');
			td.textContent = text;
			if (cls) {
				td.className = cls;
			}
			tr.appendChild(td);
			return td;
		}

		function fmt(template, values) {
			return template.replace(/%(\d+)\$s/g, function (m, i) {
				return values[parseInt(i, 10) - 1];
			});
		}

		function radio(name, value, text, checked) {
			var label = document.createElement('label');
			var input = document.createElement('input');
			input.type = 'radio';
			input.className = 'radio';
			input.name = name;
			input.value = value;
			input.checked = checked;
			label.appendChild(input);
			label.appendChild(document.createTextNode(' ' + text));
			return label;
		}

		function openDialog(list) {
			recapRows.textContent = '';
			rolesBox.textContent = '';
			var roles = {};

			list.forEach(function (s) {
				var tr = document.createElement('tr');
				var group = cell(tr, s.getAttribute('data-group'));
				if (s.getAttribute('data-role')) {
					group.appendChild(document.createTextNode(' ★'));
				}
				if (isForums) {
					cell(tr, s.getAttribute('data-forum'));
				}
				cell(tr, s.getAttribute('data-perm'));
				var orig = s.getAttribute('data-orig');
				cell(tr, labels[orig], 'permmatrix-v' + orig);
				cell(tr, labels[s.value], 'permmatrix-v' + s.value + ' permmatrix-strong');
				recapRows.appendChild(tr);

				var key = s.getAttribute('data-role-key');
				if (key && !roles[key]) {
					roles[key] = s;
				}
			});

			Object.keys(roles).forEach(function (key) {
				var s = roles[key];
				var box = document.createElement('fieldset');
				box.className = 'permmatrix-rolechoice';
				var where = s.getAttribute('data-group') + (s.getAttribute('data-forum') ? ' › ' + s.getAttribute('data-forum') : '');
				var p = document.createElement('p');
				p.textContent = fmt(form.getAttribute('data-msg-role'), [where, s.getAttribute('data-role'), s.getAttribute('data-role-usage')]);
				box.appendChild(p);
				var name = 'role_action[' + key + ']';
				box.appendChild(radio(name, 'convert', form.getAttribute('data-msg-role-convert'), true));
				box.appendChild(radio(name, 'update', fmt(form.getAttribute('data-msg-role-update'), [s.getAttribute('data-role'), s.getAttribute('data-role-usage')]), false));
				rolesBox.appendChild(box);
			});

			dialog.hidden = false;
			document.getElementById('permmatrix-confirm').focus();
		}

		function closeDialog() {
			dialog.hidden = true;
			rolesBox.textContent = '';
		}

		form.addEventListener('submit', function (e) {
			if (submitting) {
				return;
			}
			e.preventDefault();
			var list = changedSelects();
			if (list.length) {
				openDialog(list);
			}
		});

		document.getElementById('permmatrix-cancel').addEventListener('click', closeDialog);
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) {
				closeDialog();
			}
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !dialog.hidden) {
				closeDialog();
			}
		});

		document.getElementById('permmatrix-confirm').addEventListener('click', function () {
			// Only changed cells are sent.
			each(selects, function (s) {
				if (s.value === s.getAttribute('data-orig')) {
					s.disabled = true;
				}
			});
			submitting = true;
			form.submit();
		});

		window.addEventListener('beforeunload', function (e) {
			if (!submitting && changedSelects().length) {
				e.preventDefault();
				e.returnValue = form.getAttribute('data-msg-leave');
				return e.returnValue;
			}
		});

		refresh();
	});
})();
