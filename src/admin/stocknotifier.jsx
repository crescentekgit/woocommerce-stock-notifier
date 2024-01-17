import React, { Component } from "react";
import { BrowserRouter as Router, useLocation } from "react-router-dom";
import WCSNTab from "./tabs";
import SubscriberList from "./subscriberlist";

class StockNotifier_Backend extends Component {
  constructor(props) {
    super(props);
    this.state = {};
    this.Stocknotifier_backend = this.Stocknotifier_backend.bind(this);
  }

  useQuery() {
    return new URLSearchParams(useLocation().hash);
  }

  Stocknotifier_backend() {
    // For active submneu pages
    const $ = jQuery;
    const menuRoot = $("#toplevel_page_" + "wcsn-stock-notifier-setting");

    const currentUrl = window.location.href;
    const currentPath = currentUrl.substr(currentUrl.indexOf("admin.php"));

    menuRoot.on("click", "a", function () {
      const self = $(this);
      $("ul.wp-submenu li", menuRoot).removeClass("current");
      if (self.hasClass("wp-has-submenu")) {
        $("li.wp-first-item", menuRoot).addClass("current");
      } else {
        self.parents("li").addClass("current");
      }
    });

    $("ul.wp-submenu a", menuRoot).each(function (index, el) {
      if ($(el).attr("href") === currentPath) {
        $(el).parent().addClass("current");
      } else {
        $(el).parent().removeClass("current");
        // if user enter page=catalog
        if (
          $(el).parent().hasClass("wp-first-item") &&
          currentPath === "admin.php?page=wcsn-stock-notifier-setting"
        ) {
          $(el).parent().addClass("current");
        }
      }
    });
    const location = this.useQuery();
    if (location.get("tab") && location.get("tab") === "settings") {
      return (
        <WCSNTab
          model="stock_notifier-settings"
          query_name={location.get("tab")}
          subtab={location.get("subtab")}
          funtion_name={this}
        />
      );
    } else if (
      location.get("tab") &&
      location.get("tab") === "subscriber-list"
    ) {
      return <SubscriberList />;
    } else {
      return (
        <WCSNTab
          model="stock_notifier-settings"
          query_name="settings"
          subtab="general"
          funtion_name={this}
        />
      );
    }
  }

  render() {
    return (
      <Router>
        <this.Stocknotifier_backend />
      </Router>
    );
  }
}
export default StockNotifier_Backend;
