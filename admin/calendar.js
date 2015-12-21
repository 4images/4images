/**
* Filename.......: calendar.js
* Project........: Popup Calendar
* Last Modified..: $Date: 2003/01/22 13:52:00 $
* CVS Revision...: $Revision: 1.2 $
* Copyright......: 2001, 2002 Richard Heyes
*/

	calendar_layers          = new Array();
	calendar_mouseoverStatus = false;
	calendar_mouseX          = 0;
	calendar_mouseY          = 0;
	root_path                = './';

	function calendar(objName, callbackFunc)
	{
		this.today          = new Date();
		this.date           = this.today.getDate();
		this.month          = this.today.getMonth();
		this.year           = this.today.getFullYear();

		this.objName        = objName;
		this.callbackFunc   = callbackFunc;
		this.layerID        = arguments[3] ? arguments[3] : 'calendar_layer_' + calendar_layers.length;

		this.offsetX        = 5;
		this.offsetY        = 5;
		this.yearComboRange = 5;

		this.currentMonth   = this.month;
		this.currentYear    = this.year;

		this.show              = calendar_show;
		this.writeHTML         = calendar_writeHTML;
		this.setCurrentMonth   = calendar_setCurrentMonth;
		this.setCurrentYear    = calendar_setCurrentYear;

		this._getLayer         = calendar_getLayer;
		this._hideLayer        = calendar_hideLayer;
		this._showLayer        = calendar_showLayer;
		this._setLayerPosition = calendar_setLayerPosition;
		this._setHTML          = calendar_setHTML;

		// Miscellaneous
		this._getDaysInMonth   = calendar_getDaysInMonth;
		this._mouseover        = calendar_mouseover;

		calendar_layers[calendar_layers.length] = this;
	}

	function calendar_show()
	{
		// Variable declarations to prevent globalisation
		var month, year, monthnames, numdays, thisMonth, firstOfMonth;
		var ret, row, i, cssClass, linkHTML, previousMonth, previousYear;
		var nextMonth, nextYear, prevImgHTML, prevLinkHTML, nextImgHTML, nextLinkHTML;
		var monthComboOptions, monthCombo, yearComboOptions, yearCombo, html;

		this.currentMonth = month = arguments[0] != null ? arguments[0] : this.currentMonth;
		this.currentYear  = year  = arguments[1] != null ? arguments[1] : this.currentYear;

		monthnames = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
		numdays    = this._getDaysInMonth(month, year);

		thisMonth    = new Date(year, month, 1);
		firstOfMonth = thisMonth.getDay();

		// First few blanks up to first day
		ret = new Array(new Array());
		for(i=0; i<firstOfMonth; i++){
			ret[0][ret[0].length] = '<td>&nbsp;</td>';
		}

		// Main body of calendar
		row = 0;
		i   = 1;
		while(i <= numdays){
			if(ret[row].length == 7){
				ret[++row] = new Array();
			}

			/**
            * Generate this cells' HTML
            */
			cssClass = (i == this.date && month == this.month && year == this.year) ? 'calendar_today' : 'calendar_day';
			linkHTML = '<a href="javascript: ' + this.callbackFunc + '(' + i + ', ' + (Number(month) + 1) + ', ' + year + '); ' + this.objName + '._hideLayer()">' + (i++) + '</a>';
			ret[row][ret[row].length] = '<td align="center" class="' + cssClass + '">' + linkHTML + '</td>';
		}

		// Format the HTML
		for(i=0; i<ret.length; i++){
			ret[i] = ret[i].join('\n') + '\n';
		}

		previousYear  = thisMonth.getFullYear();
		previousMonth = thisMonth.getMonth() - 1;
		if(previousMonth < 0){
			previousMonth = 11;
			previousYear--;
		}

		nextYear  = thisMonth.getFullYear();
		nextMonth = thisMonth.getMonth() + 1;
		if(nextMonth > 11){
			nextMonth = 0;
			nextYear++;
		}

		prevImgHTML  = '<img src="' + root_path + 'admin/images/arrow_back.gif" border="0" />';
		prevLinkHTML = '<a href="javascript: ' + this.objName + '.show(' + previousMonth + ', ' + previousYear + ')">' + prevImgHTML + '</a>';
		nextImgHTML  = '<img src="' + root_path + 'admin/images/arrow.gif" border="0" />';
		nextLinkHTML = '<a href="javascript: ' + this.objName + '.show(' + nextMonth + ', ' + nextYear + ')">' + nextImgHTML + '</a>';

		/**
        * Build month combo
        */
		monthComboOptions = '';
		for (i=0; i<12; i++) {
			selected = (i == thisMonth.getMonth() ? 'selected="selected"' : '');
			monthComboOptions += '<option value="' + i + '" ' + selected + '>' + monthnames[i] + '</option>';
		}
		monthCombo = '<select name="months" onchange="' + this.objName + '.show(this.options[this.selectedIndex].value, ' + this.objName + '.currentYear)">' + monthComboOptions + '</select>';

		/**
        * Build year combo
        */
		yearComboOptions = '';
		for (i = thisMonth.getFullYear() - this.yearComboRange; i <= (thisMonth.getFullYear() + this.yearComboRange); i++) {
			selected = (i == thisMonth.getFullYear() ? 'selected="selected"' : '');
			yearComboOptions += '<option value="' + i + '" ' + selected + '>' + i + '</option>';
		}
		yearCombo = '<select style="border: 1px groove" name="years" onchange="' + this.objName + '.show(' + this.objName + '.currentMonth, this.options[this.selectedIndex].value)">' + yearComboOptions + '</select>';

		html = '<table border="0" bgcolor="#eeeeee">';
		html += '<tr><td>' + prevLinkHTML + '</td><td colspan="5" align="center">' + monthCombo + ' ' + yearCombo + '</td><td align="right">' + nextLinkHTML + '</td></tr>';
		html += '<tr>';
		html += '<td class="calendar_dayname">Sun</td>';
		html += '<td class="calendar_dayname">Mon</td>';
		html += '<td class="calendar_dayname">Tue</td>';
		html += '<td class="calendar_dayname">Wed</td>';
		html += '<td class="calendar_dayname">Thu</td>';
		html += '<td class="calendar_dayname">Fri</td>';
		html += '<td class="calendar_dayname">Sat</td></tr>';
		html += '<tr>' + ret.join('</tr>\n<tr>') + '</tr>';
		html += '</table>';

		this._setHTML(html);
		if (!arguments[0] && !arguments[1]) {
			this._showLayer();
			this._setLayerPosition();
		}
	}

	function calendar_writeHTML()
	{
		if (is_ie5up || is_nav6up || is_gecko) {
			document.write('<a href="javascript: ' + this.objName + '.show()"><img src="' + root_path + 'admin/images/calendar.gif" border="0" width="16" height="16" /></a>');
			document.write('<div class="calendar" id="' + this.layerID + '" onmouseover="' + this.objName + '._mouseover(true)" onmouseout="' + this.objName + '._mouseover(false)"></div>');
		}
	}

	function calendar_setCurrentYear(year)
	{
		this.currentYear  = year;
	}

	function calendar_setCurrentMonth(month)
	{
		this.currentMonth = month - 1;
	}

	function calendar_getLayer()
	{
		var layerID = this.layerID;

		if (document.getElementById(layerID)) {

			return document.getElementById(layerID);

		} else if (document.all(layerID)) {
			return document.all(layerID);
		}
	}

	function calendar_hideLayer()
	{
		this._getLayer().style.visibility = 'hidden';
	}

	function calendar_showLayer()
	{
		this._getLayer().style.visibility = 'visible';
	}

	function calendar_setLayerPosition()
	{
		this._getLayer().style.top  = (calendar_mouseY + this.offsetY) + 'px';
		this._getLayer().style.left = (calendar_mouseX + this.offsetX) + 'px';
	}

	function calendar_setHTML(html)
	{
		this._getLayer().innerHTML = html;
	}

	function calendar_getDaysInMonth(month, year)
	{
		monthdays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
		if (month != 1) {
			return monthdays[month];
		} else {
			return ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0 ? 29 : 28);
		}
	}

	function calendar_mouseover(status)
	{
		calendar_mouseoverStatus = status;
		return true;
	}

	calendar_oldOnmousemove = document.onmousemove ? document.onmousemove : new Function;

	document.onmousemove = function ()
	{
		if (is_ie5up || is_nav6up || is_gecko) {
			if (arguments[0]) {
				calendar_mouseX = arguments[0].pageX;
				calendar_mouseY = arguments[0].pageY;
			} else {
				calendar_mouseX = event.clientX + document.body.scrollLeft;
				calendar_mouseY = event.clientY + document.body.scrollTop;
				arguments[0] = null;
			}

			calendar_oldOnmousemove();
		}
	}

	calendar_oldOnclick = document.onclick ? document.onclick : new Function;

	document.onclick = function ()
	{
		if (is_ie5up || is_nav6up || is_gecko) {
			if(!calendar_mouseoverStatus){
				for(i=0; i<calendar_layers.length; ++i){
					calendar_layers[i]._hideLayer();
				}
			}

			calendar_oldOnclick(arguments[0] ? arguments[0] : null);
		}
	}