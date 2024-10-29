
import { render } from '@wordpress/element';
import { BrowserRouter} from 'react-router-dom';
import StockNotifier from "./admin/stocknotifier";

/**
 * Import the stylesheet for the plugin.
 */
import './style/main.scss';
// Render the App component into the DOM
render(<BrowserRouter><StockNotifier /></BrowserRouter>, document.getElementById('wcsn-admin-stocknotifier'));