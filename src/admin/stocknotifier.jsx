import React from "react";
import { useLocation } from "react-router-dom";
import WCSNTab from "./tabs";
import SubscriberList from "./subscriberlist";

const StockNotifierBackend = () => {
  const useQuery = () => new URLSearchParams(useLocation().hash);

  const stockNotifierBackend = () => {
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
        if (
          $(el).parent().hasClass("wp-first-item") &&
          currentPath === "admin.php?page=wcsn-stock-notifier-setting"
        ) {
          $(el).parent().addClass("current");
        }
      }
    });

    const location = useQuery();

    if (location.get("tab") && location.get("tab") === "settings") {
      return (
        <WCSNTab
          model="stock_notifier-settings"
          query_name={location.get("tab")}
          subtab={location.get("subtab")}
          funtion_name={stockNotifierBackend}
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
          funtion_name={stockNotifierBackend}
        />
      );
    }
  };

  return (
      stockNotifierBackend()
  );
};

export default StockNotifierBackend;
