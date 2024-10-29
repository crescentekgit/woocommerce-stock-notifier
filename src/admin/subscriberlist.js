/*wcsnLocalizer*/
import React, { Component } from "react";
import axios from "axios";
import { CSVLink } from "react-csv";
import DataTable, { createTheme } from "react-data-table-component";
import PropagateLoader from "react-spinners/PropagateLoader";
import { DateRangePicker } from "rsuite";

class SubscriberList extends Component {
  constructor(props) {
    super(props);
    this.state = {
      subscriber_loading: false,
      subscribe_active: "any",
      subscription_list_status_all: true,
      subscription_list_status_subscription: false,
      subscription_list_status_unsubscription: false,
      subscription_list_status_mail_sent: false,
      all_subscriber_list: [],
      data_subscriber: [],
      data_unsubscriber: [],
      data_email_sent_subscriber: [],
      data_trash_subscriber: [],
      datasubscriber: [],
      columns_subscriber_list: [],
      date_range: "",
      open_model: false,
      
    };
    this.onSubChange = this.onSubChange.bind(this);
    this.handle_subscription_live_search = this.handle_subscription_live_search.bind(this);
    this.handlesubscriptionsearch = this.handlesubscriptionsearch.bind(this);
    this.handleupdatesub = this.handleupdatesub.bind(this);
  }
  

  handleupdatesub(e) {
    this.setState({
      date_range: e,
    });

    axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
        {
          params: { date_range: e },
        }
      )
      .then((response) => {
        this.setState({
          datasubscriber: response.data,
        });
      });
  }

  onSubChange(e, name) {}

  handlesubscriptionsearch(e, status) {
    if (status === "searchproduct") {
      if (e && e.target.value.length > 2) {
        axios
          .get(
            `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/search_subscribe_by_product`,
            {
              params: {
                product: e.target.value,
                subscription_status: this.state.subscribe_active,
                date_range: this.state.date_range,
              },
            }
          )
          .then((response) => {
            this.setState({
              datasubscriber: response.data,
            });
          });
      } else {
        axios
          .get(
            `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
            {
              params: {
                date_range: this.state.date_range,
                subscription_status: this.state.subscribe_active,
              },
            }
          )
          .then((response) => {
            this.setState({
              datasubscriber: response.data,
            });
          });
      }
    }
  }

  handle_subscription_live_search(e) {
    if (e.target.value) {
      axios
        .get(
          `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/search_specific_subscribe`,
          {
            params: { email_id: e.target.value },
          }
        )
        .then((response) => {
          this.setState({
            datasubscriber: response.data,
          });
        });
    } else {
      axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
        {
          params: { date_range: this.state.date_range },
        }
      )
      .then((response) => {
        this.setState({
          datasubscriber: response.data,
        });
      });
    }
  }

  handle_subscription_status_check(e, type) {
    if ( type === "subscribe" ) {
      this.setState({
        subscribe_active: "wcsn_subscribed",
        subscription_list_status_all: false,
        subscription_list_status_subscription: true,
        subscription_list_status_unsubscription: false,
        subscription_list_status_mail_sent: false,
      });
      // subscribe status
      axios
        .get(
          `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
          {
            params: { subscription_status: "wcsn_subscribed" },
          }
        )
        .then((response) => {
          this.setState({
            datasubscriber: response.data,
          });
        });
    }

    if (type === "unsubscribe") {
      // unsubscribe status
      this.setState({
        subscribe_active: "wcsn_unsubscribed",
        subscription_list_status_all: false,
        subscription_list_status_subscription: false,
        subscription_list_status_unsubscription: true,
        subscription_list_status_mail_sent: false,
      });
      axios
        .get(
          `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
          {
            params: { subscription_status: "wcsn_unsubscribed" },
          }
        )
        .then((response) => {
          this.setState({
            datasubscriber: response.data,
          });
        });
    }

    if (type === "mail_sent") {
      // refunded status
      this.setState({
        subscribe_active: "wcsn_mailsent",
        subscription_list_status_all: false,
        subscription_list_status_subscription: false,
        subscription_list_status_unsubscription: false,
        subscription_list_status_mail_sent: true,
      });
      axios
        .get(
          `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
          {
            params: { subscription_status: "wcsn_mailsent" },
          }
        )
        .then((response) => {
          this.setState({
            datasubscriber: response.data,
          });
        });
    }

    if (type === "all") {
      this.setState({
        subscribe_active: "any",
        subscription_list_status_all: true,
        subscription_list_status_subscription: false,
        subscription_list_status_unsubscription: false,
        subscription_list_status_mail_sent: false,
      });

      axios
        .get(
          `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
          {
            params: { date_range: this.state.date_range },
          }
        )
        .then((response) => {
          this.setState({
            datasubscriber: response.data,
          });
        });
    }
  }

  loading_funtions = (e) => {
    // subscribe status
    axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/no_of_subscribe_list`,
        {
          params: {
            subscribtion_status: "wcsn_subscribed",
            date_range: this.state.date_range,
          },
        }
      )
      .then((response) => {
        this.setState({
          data_subscriber: response.data,
        });
      });

    // unsubscribe status
    axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/no_of_subscribe_list`,
        {
          params: {
            subscribtion_status: "wcsn_unsubscribed",
            date_range: this.state.date_range,
          },
        }
      )
      .then((response) => {
        this.setState({
          data_unsubscriber: response.data,
        });
      });

    // mail sent status
    axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/no_of_subscribe_list`,
        {
          params: {
            subscribtion_status: "wcsn_mailsent",
            date_range: this.state.date_range,
          },
        }
      )
      .then((response) => {
        this.setState({
          data_email_sent_subscriber: response.data,
        });
      });
  };

  componentDidMount() {
    this.loading_funtions("");
    axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/show_subscribe_from_status_list`,
        {
          params: { date_range: this.state.date_range },
        }
      )
      .then((response) => {
        this.setState({
          datasubscriber: response.data,
          all_subscriber_list: response.data,
          subscriber_loading: true,
        });
      });

    wcsnLocalizer.columns_subscriber.map((data_sub, index_sub) => {
      let data_selector_sub = "";
      let set_for_dynamic_column_sub = "";
      data_selector_sub = data_sub.selector_choice;
      data_sub.selector = (row) => (
        <div
          dangerouslySetInnerHTML={{
            __html: row[data_selector_sub],
          }}
        ></div>
      );

      this.state.columns_subscriber_list[index_sub] = data_sub;
      set_for_dynamic_column_sub = this.state.columns_subscriber_list;
      this.state.columns_subscriber_list = set_for_dynamic_column_sub;
    });
  }
  
  render() {
    return (
      <div>
        {
          <div className="wcsn-subscriber-list">
            <div className="wcsn-container">
              <div className="wcsn-middle-container-wrapper">
                <div className="wcsn-page-title">
                  <p>Subscriber List</p>
                  <div className="pull-right">
                    <CSVLink
                      data={this.state.datasubscriber}
                      headers={wcsnLocalizer.columns_subscriber_list}
                      filename={"subscriber_list.csv"}
                      className="wcsn-btn btn-purple"
                    >
                      <i className="wcsn-font icon-download"></i>
                      {wcsnLocalizer.download_csv}
                    </CSVLink>
                  </div>
                </div>
                <div className="wcsn-search-and-multistatus-wrap">
                  <ul className="wcsn-multistatus-ul">
                    <li
                      className={`wcsn-multistatus-item ${
                        this.state.subscription_list_status_all
                          ? "status-active"
                          : ""
                      }`}
                    >
                      <div
                        className="wcsn-multistatus-check-all status-active"
                        onClick={(e) =>
                          this.handle_subscription_status_check(e, "all")
                        }
                      >
                        {wcsnLocalizer.subscription_page_string.all} (
                        {this.state.all_subscriber_list.length})
                      </div>
                    </li>
                    <li className="wcsn-multistatus-item wcsn-divider"></li>
                    <li
                      className={`wcsn-multistatus-item ${
                        this.state.subscription_list_status_subscription
                          ? "status-active"
                          : ""
                      }`}
                    >
                      <div
                        className="wcsn-multistatus-check-subscribe"
                        onClick={(e) =>
                          this.handle_subscription_status_check(e, "subscribe")
                        }
                      >
                        {
                          wcsnLocalizer.subscription_page_string
                            .subscribe
                        }{" "}
                        ({this.state.data_subscriber})
                      </div>
                    </li>
                    <li className="wcsn-multistatus-item wcsn-divider"></li>
                    <li
                      className={`wcsn-multistatus-item ${
                        this.state.subscription_list_status_unsubscription
                          ? "status-active"
                          : ""
                      }`}
                    >
                      <div
                        className="wcsn-multistatus-check-unpaid"
                        onClick={(e) =>
                          this.handle_subscription_status_check(
                            e,
                            "unsubscribe"
                          )
                        }
                      >
                        {
                          wcsnLocalizer.subscription_page_string
                            .unsubscribe
                        }{" "}
                        ({this.state.data_unsubscriber})
                      </div>
                    </li>
                    <li className="wcsn-multistatus-item wcsn-divider"></li>
                    <li
                      className={`wcsn-multistatus-item ${
                        this.state.subscription_list_status_mail_sent
                          ? "status-active"
                          : ""
                      }`}
                    >
                      <div
                        className="wcsn-multistatus-check-unpaid"
                        onClick={(e) =>
                          this.handle_subscription_status_check(e, "mail_sent")
                        }
                      >
                        {
                          wcsnLocalizer.subscription_page_string
                            .mail_sent
                        }{" "}
                        ({this.state.data_email_sent_subscriber})
                      </div>
                    </li>
                  </ul>
                </div>
                <div className="wcsn-wrap-bulk-all-date">
                  <div className="wcsn-header-search-section">
                    <label>
                      <i className="wcsn-font icon-search"></i>
                    </label>
                    <input
                      type="text"
                      placeholder={
                        wcsnLocalizer.subscription_page_string.search
                      }
                      onChange={this.handle_subscription_live_search}
                    />
                  </div>
                  <div className="wcsn-header-search-section">
                    <input
                      type="text"
                      placeholder={
                        wcsnLocalizer.subscription_page_string
                          .show_product
                      }
                      onChange={(e) =>
                        this.handlesubscriptionsearch(e, "searchproduct")
                      }
                    />
                  </div>

                  <DateRangePicker
                    placeholder={
                      wcsnLocalizer.subscription_page_string.daterenge
                    }
                    onChange={(e) => this.handleupdatesub(e)}
                  />
                </div>
                

                {this.state.columns_subscriber_list &&
                this.state.columns_subscriber_list.length > 0 &&
                this.state.subscriber_loading ? (
                  <div className="wcsn-backend-datatable-wrapper">
                    <DataTable
                      columns={this.state.columns_subscriber_list}
                      data={this.state.datasubscriber}
                      pagination
                    />
                  </div>
                ) : (
                  <div className="loader_sign">
                    <PropagateLoader color="#e35047" />
                  </div>
                )}
              </div>
            </div>
          </div>
        }
        ;
      </div>
    );
  }
}
export default SubscriberList;
