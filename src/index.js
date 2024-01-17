import { render } from '@wordpress/element';
import StockNotifier from "./admin/stocknotifier";

/**
 * Import the stylesheet for the plugin.
 */
import './style/main.scss';
// Render the App component into the DOM
render(<StockNotifier />, document.getElementById('wcsn-admin-stocknotifier'));
