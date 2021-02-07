<script>
window.onload = function() { 
    Agenda.load(@json($events_json))
    };

function addDays(date, days) {
  var result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}

function $E(name, options) {
	var node = document.createElement(name);
	options = options || {};
	if (options.className)
		node.className = options.className;
	for (property in options.attributes || {})
		node[property] = options.attributes[property];
	for (name in options.style || {})
		node.style[name] = options.style[name];
	for (var i = 0; i < (options.children || []).length; ++i)
		node.appendChild(options.children[i]);
	return node;
}
function $T(text) {
	return document.createTextNode(text || '');
}
function $O(element, callback, event) {
	if (element.addEventListener)
		element.addEventListener(event || 'click', callback, false);
	else if (element.attachEvent)
		element.attachEvent('on' + (event || 'click'), callback);
	else
		W.Exception.create(new Error('Eventhandler could not be set!'));
	return element;
}
function bind(callback) {
	var args = [];
	args.push.apply(args, arguments);
	return function() {
		var cpy = args.slice(1);
		cpy.push.apply(cpy, arguments);
		callback.apply({}, cpy);
	}
}
var undefined;

Agenda = {
	monate: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
	date: new Date(new Date().getFullYear(), new Date().getMonth() - new Date().getMonth() % 3, 1),
	start: null,
	end: null,
	reserved: {},
	matrix: [],
	// initial calendar setup
	load: function(records) {
		for (var i = 0, date, end; i < records.length; ++i) {
			date = new Date(records[i].start.y, records[i].start.m, records[i].start.d);
			end = new Date(records[i].end.y, records[i].end.m, records[i].end.d);
			if (Date.parse(date) == Date.parse(end))
				this.reserved[Date.parse(date)] = { lState: records[i].state, rState: records[i].state, id: records[i].id };
			else {
				if (records[i].start.h) {
					this.reserved[Date.parse(date)] = { lState: (this.reserved[Date.parse(date)] || {}).lState, rState: records[i].state, id: records[i].id };
					date.setDate(date.getDate() + 1);
				}
				if (Date.parse(date) <= Date.parse(end)) {
					for (; Date.parse(date) != Date.parse(end); date.setDate(date.getDate() + 1))
						this.reserved[Date.parse(date)] = { lState: records[i].state, rState: records[i].state, id: records[i].id };
					if (Date.parse(date) == Date.parse(end) && records[i].end.h)
						this.reserved[Date.parse(date)] = { lState: records[i].state, rState: (this.reserved[Date.parse(date)] || {}).rState, id: records[i].id };
					else
						this.reserved[Date.parse(date)] = { lState: records[i].state, rState: records[i].state, id: records[i].id };
				}
			}
		}
		for (var i = 0; i < 9; ++i) {
			this.matrix[i] = [];
			$('#agendaMonat' + i).append("<tr>" +
				"<td class='hk-agenda__label'>Mo</td>" +
				"<td class='hk-agenda__label'>Di</td>" +
				"<td class='hk-agenda__label'>Mi</td>" +
				"<td class='hk-agenda__label'>Do</td>" +
				"<td class='hk-agenda__label'>Fr</td>" +
				"<td class='hk-agenda__label'>Sa</td>" +
				"<td class='hk-agenda__label'>So</td>" +
				"</tr>");
			for (var j = 0; j < 6; ++j) {
				this.matrix[i][j] = [];
				for (var k = 0, row = []; k < 7; ++k) {
					this.matrix[i][j][k] = { };
					row.push(this.matrix[i][j][k].node = $O(
						$E('td', { className: 'hk-agenda__day--inactive', children: [$T(' ')]}),
						bind(function(i, j, k){ Agenda.select(i, j, k); }, i, j, k)
					));
				}
				document.getElementById('agendaMonat' + i).appendChild($E('tr', { className: 'hk-agenda__week', children: row }));
			}
		}
		this.apply();
	},
	prev: function(number) {
		this.date = new Date(this.date.getFullYear(), this.date.getMonth() - number, 1);
		this.apply();
	},
	next: function(number) {
		this.date = new Date(this.date.getFullYear(), this.date.getMonth() + number, 1);
		this.apply();
	},
	change: function() {
		start_date = new Date(document.getElementById('start_date').value);
		this.start = new Date(start_date.getFullYear(), start_date.getMonth(), start_date.getDate());
		end_date = new Date(document.getElementById('end_date').value);
		this.end = new Date(end_date.getFullYear(), end_date.getMonth(), end_date.getDate());
		var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
		if (Date.parse(this.start) > Date.parse(this.end)) {
			this.end = new Date(today);
			$('#reservation_error').show();
			setTimeout(function() { $('#reservation_error').hide(); }, 5000);
			$('#reservation_error_2').show();
			setTimeout(function() { $('#reservation_error_2').hide(); }, 5000);
		}
		if (Date.parse(today) > Date.parse(this.start) || Date.parse(today) > Date.parse(this.end)) {
			this.end = new Date(today);
			this.start = new Date(today);
			$('#reservation_error').show();
			setTimeout(function() { $('#reservation_error').hide(); }, 5000);
			$('#reservation_error_2').show();
			setTimeout(function() { $('#reservation_error_2').hide(); }, 5000);
		}
		this.apply();
	},
	// after clicking a date in the calendar
	select: function(i, j, k) {
		if (this.end !== null) {
			this.start = null;
			this.end = null;
		}
			console.log(this.matrix)
		if (this.start === null && (this.reserved[Date.parse(this.matrix[i][j][k].date)] || {}).rState);
		else if (this.start === null)
			this.start = this.matrix[i][j][k].date;
		else {
			this.end = this.matrix[i][j][k].date;
			if (Date.parse(this.start) > Date.parse(this.end)) {
				var tmp = this.end;
				this.end = this.start;
				this.start = tmp;
			}
		}
		var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
		if (Date.parse(today) >= Date.parse(this.start) || Date.parse(today) >= Date.parse(this.end)) {
			this.end = new Date(today);
			this.start = new Date(today);
			$('#reservation_error').show();
			setTimeout(function() { $('#reservation_error').hide(); }, 5000);
			$('#reservation_error_2').show();
			setTimeout(function() { $('#reservation_error_2').hide(); }, 5000);
		}
			console.log(this.matrix)
		this.apply();
	},
	// reset selection
	clear: function() {
		this.start = null;
		this.end = null;
		this.apply();
	},
	// repaint calendar
	apply: function() {
		this.index = {};
		for (var i = 0, month; i < 9; ++i) {
			var date = new Date(this.date.getFullYear(), this.date.getMonth() + i, 1);
			document.getElementById('agendaTitel' + i).firstChild.nodeValue = this.monate[month = date.getMonth()] + ' ' + date.getFullYear();
			for (var k = 0; k < (date.getDay() == 0 ? 6 : date.getDay() - 1); ++k) {
				this.matrix[i][0][k].node.className = 'hk-agenda__day--inactive';
				this.matrix[i][0][k].node.firstChild.nodeValue = ' ';
				this.matrix[i][0][k].date = null;
			}
			for (var j = 0; j < 6 && date.getMonth() == month; ++j, k = 0) {
				this.matrix[i][j][k].node.parentNode.style.display = '';
				for (; k < 7 && date.getMonth() == month; ++k) {
					this.matrix[i][j][k].node.firstChild.nodeValue = String(date.getDate());
					this.matrix[i][j][k].date = new Date(date);
					this.matrix[i][j][k].state = ((this.reserved[Date.parse(date)] || {}).lState || 'F') + ((this.reserved[Date.parse(date)] || {}).rState || 'F');
					this.index[Date.parse(date)] = { i: i, j: j, k: k };
					date.setDate(date.getDate() + 1);
				}
				for (; k < 7; ++k) {
					this.matrix[i][j][k].node.className = 'hk-agenda__day--inactive';
					this.matrix[i][j][k].node.firstChild.nodeValue = '';
					this.matrix[i][j][k].date = null;
				}
			}
			for (; j < 6; ++j)
				this.matrix[i][j][0].node.parentNode.style.display = 'none';
		}
		if (this.start !== null && (!this.reserved[Date.parse(this.start)] || !this.reserved[Date.parse(this.start)].rState)) {
			var date = new Date(this.start);
			var end = new Date(this.end || date);
			console.log(date)
			console.log(this.matrix)

			// console.log(date + ' ' + end)
			// console.log(this.matrix)
			if (Date.parse(date) == Date.parse(end)) {
				if (this.index[Date.parse(date)]) {
					if (this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state.charAt(0) == 'F')
						this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state = 'SS';
					else
						this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state = 
							this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state.charAt(0) + 'S';
				}
			} else if (date < end) {
				if (!(this.reserved[Date.parse(date)] || {}).rState && this.index[Date.parse(date)]) {
					this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state
						=  this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state.charAt(0) + 'S';
					date.setDate(date.getDate() + 1);
				}
				for (; Date.parse(date) != Date.parse(end); date.setDate(date.getDate() + 1)){
					console.log(date + ': ' + Date.parse(date));
					// console.log(this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state);
					if ((this.reserved[Date.parse(date)] || {}).rState)
						break;
					else if (this.index[Date.parse(date)])
						this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state = 'SS';
					// console.log(this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state);
				}
				if (this.index[Date.parse(date)] && !(this.reserved[Date.parse(date)] || {}).lState)
					this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state
						= 'S' + this.matrix[this.index[Date.parse(date)].i][this.index[Date.parse(date)].j][this.index[Date.parse(date)].k].state.charAt(1);
			}
			// console.log(this.matrix)
			// console.log(this.start + ' ' + this.end)
			start_date = addDays(this.start, +1);
			end_date = addDays(date, +1);
			$("#start_date").val(start_date.toISOString().split('T')[0]);
			$("#end_date").val(end_date.toISOString().split('T')[0]);
		} else {
			start_date = addDays(new Date(), +1);
			end_date = addDays(new Date(), +1);
			$("#start_date").val(start_date.toISOString().split('T')[0]);
			$("#end_date").val(end_date.toISOString().split('T')[0]);

		}
		var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
		for (date in this.index) {
			this.matrix[this.index[date].i][this.index[date].j][this.index[date].k].node.className = 'hk-agenda__day--' + this.matrix[this.index[date].i][this.index[date].j][this.index[date].k].state;
			if (this.matrix[this.index[date].i][this.index[date].j][this.index[date].k].state == 'FF') {
				this.matrix[this.index[date].i][this.index[date].j][this.index[date].k].node.className += (this.index[date].k == 5 ? ' hk-agenda__day--sat' : '') + (this.index[date].k == 6 ? ' hk-agenda__day--sun' : '');
			}
			if (today > date) {
				this.matrix[this.index[date].i][this.index[date].j][this.index[date].k].node.className += ' hk-agenda__day--past';
			}
			try {
				let dateInstance = new Date(parseInt(date));
				let dateString = dateInstance.getFullYear() + '-' + ((dateInstance.getMonth() + 1) < 10 ? '0' : '') + (dateInstance.getMonth() + 1) + '-' + (dateInstance.getDate() < 10 ? '0' : '') + dateInstance.getDate();
				this.matrix[this.index[date].i][this.index[date].j][this.index[date].k].node.className += ' date-' + dateString;
			} catch (e) {}
		}
	}
}
</script>