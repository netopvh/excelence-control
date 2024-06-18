/*
 *  Document   : datatables.js
 *  Author     : pixelcave
 *  Description: Using custom JS code to init DataTables plugin
 */

// DataTables, for more examples you can check out https://www.datatables.net/
class pageDashboard {
	/*
	 * Init DataTables functionality
	 *
	 */
	static initCharts() {
		// Override a few default classes

	}

	/*
	 * Init functionality
	 *
	 */
	static init() {
		this.initCharts();
	}
}

// Initialize when page loads
Codebase.onLoad(() => pageDashboard.init());
