/* global wcsnLocalizer */
import React, { Component } from "react";
import { BrowserRouter as Router, Link } from "react-router-dom";
import axios from "axios";
import FormFields from "./formfields";
import PropagateLoader from "react-spinners/PropagateLoader";
import { css } from "@emotion/react";

const override = css`
  display: block;
  margin: 0 auto;
  border-color: red;
`;

export default class TabSection extends Component {
  state = {};
  constructor(props) {
    super(props);
    this.state = {
      fetch_admin_tabs: [],
      current: {},
      current_url: "",
    };
  }

  renderTab = () => {
    const horizontally = this.props.horizontally;
    const query_name = this.props.query_name;

    if (this.props.subtab !== this.state.current_url) {
      axios({
        url: `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/fetch_admin_tabs`,
      }).then((response) => {
        this.setState({
          fetch_admin_tabs: response.data
            ? response.data[this.props.model]
            : [],
          current_url: this.props.subtab,
        });
      });
    }
    const model = this.state.fetch_admin_tabs
      ? this.state.fetch_admin_tabs
      : [];
    const TabUI =
      Object.entries(model).length > 0
        ? Object.entries(model).map((m, index) => {
            return this.props.subtab === m[0] ? (
              <div className="wcsn-tab-description-start">
                <div className="wcsn-tab-name">{m[1].tablabel}</div>
                <p>{m[1].description}</p>
              </div>
            ) : (
              ""
            );
          })
        : "";
    const TabUIContent = (
      <div className={`wcsn-general-wrapper wcsn-${this.props.subtab}`}>
        <div className="wcsn-container wcsn-tab-banner-wrap">
          <div
            className={`wcsn-middle-container-wrapper ${
              horizontally ? "wcsn-horizontal-tabs" : "wcsn-vertical-tabs"
            }`}
          >
            
            <div className="wcsn-middle-child-container">
              {this.props.no_tabs ? (
                ""
              ) : (
                <div className="wcsn-current-tab-lists">
                  {Object.entries(model).length > 0
                    ? Object.entries(model).map((m, index) => {
                        return m[1].link ? (
                          <a className={m[1].class} href={m[1].link}>
                            {m[1].icon ? (
                              <i className={`stock-notifier-icon ${m[1].icon}`}></i>
                            ) : (
                              ""
                            )}
                            {m[1].tablabel}
                          </a>
                        ) : (
                          <Link
                            className={
                              this.props.subtab === m[0]
                                ? "active-current-tab"
                                : ""
                            }
                            to={`?page=wcsn-stock-notifier-setting#&tab=${query_name}&subtab=${m[0]}`}
                          >
                            {m[1].icon ? (
                              <i className={`stock-notifier-icon ${m[1].icon}`}></i>
                            ) : (
                              ""
                            )}
                            {m[1].tablabel}
                          </Link>
                        );
                      })
                    : ""}
                </div>
              )}
              <div className="wcsn-tab-content">
              {this.props.tab_description && this.props.tab_description === "no"
              ? ""
              : TabUI}
                {model &&
                Object.entries(model).length > 0 &&
                this.props.subtab === this.state.current_url ? (
                  Object.entries(model).map((m, index) =>
                    m[0] === this.props.subtab &&
                    m[1].modulename &&
                    m[1].modulename.length > 0 ? (
                      <FormFields
                        key={`dynamic-form-${m[0]}`}
                        title={m[1].tablabel}
                        defaultValues={this.state.current}
                        model={m[1].modulename}
                        method="post"
                        modulename={m[0]}
                        url={`wc_stocknotifier/v1/${m[1].apiurl}`}
                        submitbutton="true"
                      />
                    ) : (
                      ""
                    )
                  )
                ) : (
                  <PropagateLoader color="#36d7b7" />
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    );
    return TabUIContent;
  };

  render() {
    return this.renderTab();
  }
}
