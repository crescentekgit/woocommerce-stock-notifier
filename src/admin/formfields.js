/* global wcsnLocalizer */
import React from "react";
import Select from "react-select";
import axios from "axios";

export default class FormFields extends React.Component {
  state = {};
  constructor(props) {
    super(props);
    this.state = {
      open_model: false,
      datamclist: [],
      from_loading: false,
      errordisplay: "",
    };

    this.handleMouseEnter = this.handleMouseEnter.bind(this);
    this.handleMouseLeave = this.handleMouseLeave.bind(this);
    this.handleGetMailchimpList = this.handleGetMailchimpList.bind(this);
    this.handleOnChangeColor = this.handleOnChangeColor.bind(this);
    this.handleGetButtonColorState = this.handleGetButtonColorState.bind(this);
    this.handleOnChangerange = this.handleOnChangerange.bind(this);
    this.handleDragEnd = this.handleDragEnd.bind(this);
  }

  handleDragEnd() {
    if (this.props.submitbutton && this.props.submitbutton === "false") {
      setTimeout(() => {
        this.onSubmit("");
      }, 10);
    }
  }

  handleOnChangerange(e, target) {
    this.setState({
      subscribe_button_font_size:
        target === "subscribe_button_font_size"
          ? e.target.value
          : this.state.subscribe_button_font_size,
      subscribe_button_border_radious:
        target === "subscribe_button_border_radious"
          ? e.target.value
          : this.state.subscribe_button_border_radious,
      subscribe_button_border_size:
        target === "subscribe_button_border_size"
          ? e.target.value
          : this.state.subscribe_button_border_size,
    });
  }

  handleMouseEnter(e) {
    this.setState({
      hover_on: true,
    });
  }

  handleMouseLeave(e) {
    this.setState({
      hover_on: false,
    });
  }

  handleGetButtonColorState() {
    axios
      .get(
        `${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/get_button_data`
      )
      .then((response) => {
        this.setState({
          form_description_text_color: response.data.form_description_text_color,
          subscribe_button_background_color: response.data.subscribe_button_background_color,
          subscribe_button_border_color: response.data.subscribe_button_border_color,
          subscribe_button_text_color: response.data.subscribe_button_text_color,
          subscribe_button_background_color_onhover: response.data.subscribe_button_background_color_onhover,
          subscribe_button_text_color_onhover: response.data.subscribe_button_text_color_onhover,
          subscribe_button_border_color_onhover: response.data.subscribe_button_border_color_onhover,

          subscribe_button_font_size: response.data.subscribe_button_font_size,
          subscribe_button_border_radious: response.data.subscribe_button_border_radious,
          subscribe_button_border_size: response.data.subscribe_button_border_size,
        });
      });
  }

  handleOnChangeColor(e, target) {
    this.setState({
      form_description_text_color:
        target === "form_description_text_color"
          ? e.target.value
          : this.state.form_description_text_color,

      subscribe_button_text_color:
        target === "subscribe_button_text_color"
          ? e.target.value
          : this.state.subscribe_button_text_color,

      subscribe_button_background_color:
        target === "subscribe_button_background_color"
          ? e.target.value
          : this.state.subscribe_button_background_color,

      subscribe_button_border_color:
        target === "subscribe_button_border_color"
          ? e.target.value
          : this.state.subscribe_button_border_color,

      subscribe_button_background_color_onhover:
        target === "subscribe_button_background_color_onhover"
          ? e.target.value
          : this.state.subscribe_button_background_color_onhover,

      subscribe_button_border_color_onhover:
        target === "subscribe_button_border_color_onhover"
          ? e.target.value
          : this.state.subscribe_button_border_color_onhover,

      subscribe_button_text_color_onhover:
        target === "subscribe_button_text_color_onhover"
          ? e.target.value
          : this.state.subscribe_button_text_color_onhover,
    });

    if (this.props.submitbutton && this.props.submitbutton === "false") {
      setTimeout(() => {
        this.onSubmit("");
      }, 10);
    }
  }

  handleGetMailchimpList() {
    
  }

  onSubmit = (e) => {
    // block to refresh pages
    const prop_submitbutton =
      this.props.submitbutton && this.props.submitbutton === "false"
        ? ""
        : "true";
    if (prop_submitbutton) {
      e.preventDefault();
    }
    this.setState({ from_loading: true });

    axios({
      method: this.props.method,
      url: wcsnLocalizer.apiUrl + "/" + this.props.url,
      data: {
        model: this.state,
        modulename: this.props.modulename,
      },
    }).then((res) => {
      this.setState({
        from_loading: false,
        errordisplay: res.data.error,
      });
      setTimeout(() => {
        this.setState({ errordisplay: "" });
      }, 2000);
      if (res.data.redirect_link) {
        window.location.href = res.data.redirect_link;
      }
    });
  };

  componentDidMount() {
    if (this.props.modulename == "form_personalize") {
      this.handleGetButtonColorState();
    }

    //Fetch all datas
    this.props.model.map((m) => {
      this.setState({
        [m.key]: m.database_value,
      });
    });

    let $ = jQuery;
    $(document).ready(function () {
      setTimeout(function () {
        const allRanges = document.querySelectorAll(
          ".wcsn-progress-picker-wrap"
        );
        allRanges.forEach((wrap) => {
          const range = wrap.querySelector("input.wcsn-setting-range-picker");
          const bubble = wrap.querySelector(".bubble");

          range.addEventListener("input", () => {
            setBubble(range, bubble);
          });
          setBubble(range, bubble);
        });
      }, 2000);

      function setBubble(range, bubble) {
        const max = range.max ? range.max : 100;
        bubble.style.left = (range.value / max) * 100 + "%";
      }
    });
  }

  onChange = (e, key, type = "single", from_type = "", array_values = []) => {
    if (type === "single") {
      if (from_type === "select") {
        this.setState(
          {
            [key]: array_values[e.index],
          },
          () => {}
        );
      } else if (from_type === "mailchimp_select") {
        this.setState(
          {
            [key]: array_values[e.index],
          },
          () => {}
        );
      } else if (from_type === "multi-select") {
        this.setState(
          {
            [key]: e,
          },
          () => {}
        );
      } else if (from_type === "text_api") {
        this.setState(
          {
            [key]: e.target.value,
          },
          () => {}
        );
        this.setState({
          datamclist: [],
        });
        this.setState({
          selected_mailchimp_list: "",
        });
      } else {
        this.setState(
          {
            [key]: e.target.value,
          },
          () => {}
        );
      }
    } else {
      // Array of values (e.g. checkbox): TODO: Optimization needed.
      const found = this.state[key]
        ? this.state[key].find((d) => d === e.target.value)
        : false;

      if (found) {
        const data = this.state[key].filter((d) => {
          return d !== found;
        });
        this.setState({
          [key]: data,
        });
      } else {
        const others = this.state[key] ? [...this.state[key]] : [];
        this.setState({
          [key]: [e.target.value, ...others],
        });
      }
    }
    if (this.props.submitbutton && this.props.submitbutton === "false") {
      if (key != "password") {
        setTimeout(() => {
          this.onSubmit("");
        }, 10);
      }
    }
  };

  renderForm = () => {
    const model = this.props.model;
    const formUI = model.map((m, index) => {
      const key = m.key;
      const type = m.type || "text";
      const props = m.props || {};
      const name = m.name;
      let value = m.value;
      const placeholder = m.placeholder;
      const limit = m.limit;
      let input = "";

      const target = key;

      value = this.state[target] || "";

      if (m.restricted_page && m.restricted_page === this.props.location) {
        return false;
      }

      // If no array key found
      if (!m.key) {
        return false;
      }

      // for select selection
      if (
        m.depend &&
        this.state[m.depend] &&
        this.state[m.depend].value &&
        this.state[m.depend].value != m.dependvalue
      ) {
        return false;
      }

      // for radio button selection
      if (
        m.depend &&
        this.state[m.depend] &&
        !this.state[m.depend].value &&
        this.state[m.depend] != m.dependvalue
      ) {
        return false;
      }

      // for checkbox selection
      if (
        m.depend_checkbox &&
        this.state[m.depend_checkbox] &&
        this.state[m.depend_checkbox].length === 0
      ) {
        return false;
      }

      // for checkbox selection
      if (
        m.not_depend_checkbox &&
        this.state[m.not_depend_checkbox] &&
        this.state[m.not_depend_checkbox].length > 0
      ) {
        return false;
      }

      if (m.depend && !this.state[m.depend]) {
        return false;
      }

      if (type === "text" || "url" || "password" || "email" || "number") {
        input = (
          <div className="wcsn-settings-basic-input-class">
            <input
              {...props}
              className="wcsn-setting-form-input"
              type={type}
              key={key}
              id={m.id}
              placeholder={placeholder}
              name={name}
              value={value}
              onChange={(e) => {
                this.onChange(e, target);
              }}
            />
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "customize_table") {
        input = (
          <div class="editor-left side">
            <div class="left_side_wrap">
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.form_dec}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(e, "form_description_text_color");
                  }}
                  value={this.state.form_description_text_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.submit_button_text}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(e, "subscribe_button_text_color");
                  }}
                  value={this.state.subscribe_button_text_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.background}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(e, "subscribe_button_background_color");
                  }}
                  value={this.state.subscribe_button_background_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.border}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(e, "subscribe_button_border_color");
                  }}
                  value={this.state.subscribe_button_border_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.hover_background}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(
                      e,
                      "subscribe_button_background_color_onhover"
                    );
                  }}
                  value={this.state.subscribe_button_background_color_onhover}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.hover_border}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(e, "subscribe_button_border_color_onhover");
                  }}
                  value={this.state.subscribe_button_border_color_onhover}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.hover_text}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    this.handleOnChangeColor(e, "subscribe_button_text_color_onhover");
                  }}
                  value={this.state.subscribe_button_text_color_onhover}
                />
              </div>
            </div>
            <div class="right_side_wrap">
              <div className="wcsn-size-picker-wrap">
                {wcsnLocalizer.setting_string.font_size}
                <div className="wcsn-progress-picker-wrap">
                  <input
                    {...props}
                    className="wcsn-setting-range-picker"
                    id="subscribe_button_font_size"
                    type="range"
                    min="0"
                    max="30"
                    value={this.state.subscribe_button_font_size}
                    onChange={(e) => {
                      this.handleOnChangerange(e, "subscribe_button_font_size");
                    }}
                    onMouseUp={this.handleDragEnd}
                    onTouchEnd={this.handleDragEnd}
                  />
                  <output class="bubble">
                    {this.state.subscribe_button_font_size
                      ? this.state.subscribe_button_font_size
                      : 0}
                    px
                  </output>
                </div>
              </div>
              <div className="wcsn-size-picker-wrap">
                {wcsnLocalizer.setting_string.border_radius}
                <div className="wcsn-progress-picker-wrap">
                  <input
                    {...props}
                    className="wcsn-setting-range-picker"
                    id="subscribe_button_border_radious"
                    type="range"
                    min="0"
                    max="100"
                    value={this.state.subscribe_button_border_radious}
                    onChange={(e) => {
                      this.handleOnChangerange(e, "subscribe_button_border_radious");
                    }}
                    onMouseUp={this.handleDragEnd}
                    onTouchEnd={this.handleDragEnd}
                  />
                  <output class="bubble">
                    {this.state.subscribe_button_border_radious
                      ? this.state.subscribe_button_border_radious
                      : 0}
                    px
                  </output>
                </div>
              </div>
              <div className="wcsn-size-picker-wrap">
                {wcsnLocalizer.setting_string.border_size}
                <div className="wcsn-progress-picker-wrap">
                  <input
                    {...props}
                    className="wcsn-setting-range-picker"
                    id="subscribe_button_border_size"
                    type="range"
                    min="0"
                    max="10"
                    value={this.state.subscribe_button_border_size}
                    onChange={(e) => {
                      this.handleOnChangerange(e, "subscribe_button_border_size");
                    }}
                    onMouseUp={this.handleDragEnd}
                    onTouchEnd={this.handleDragEnd}
                  />
                  <output class="bubble">
                    {this.state.subscribe_button_border_size
                      ? this.state.subscribe_button_border_size
                      : 0}
                    px
                  </output>
                </div>
              </div>
            </div>
          </div>
        );
      }

      if (type === "section") {
        input = <div className="wcsn-setting-section-divider">&nbsp;</div>;
      }

      if (type === "heading") {
        input = (
          <div className="wcsn-setting-section-header">
            {m.blocktext ? (
              <h5
                dangerouslySetInnerHTML={{
                  __html: m.blocktext,
                }}
              ></h5>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "color") {
        input = (
          <div className="wcsn-settings-color-picker-parent-class">
            <input
              {...props}
              className="wcsn-setting-color-picker"
              type={type}
              key={key}
              id={m.id}
              name={name}
              value={value}
              onChange={(e) => {
                this.onChange(e, target);
              }}
            />
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "blocktext") {
        input = (
          <div className="wcsn-blocktext-class">
            {m.blocktext ? (
              <p
                className="wcsn-settings-metabox-description-code"
                dangerouslySetInnerHTML={{
                  __html: m.blocktext,
                }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "textarea") {
        input = (
          <div className="wcsn-setting-from-textarea">
            <textarea
              {...props}
              className={m.class ? m.class : "wcsn-form-input"}
              key={key}
              maxLength={limit}
              placeholder={placeholder}
              name={name}
              value={value}
              rows="4"
              cols="50"
              onChange={(e) => {
                this.onChange(e, target);
              }}
            />
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "select") {
        const options_data = [];
        const defaultselect = [];
        input = m.options.map((o, index) => {
          if (o.selected) {
            defaultselect[index] = {
              value: o.value,
              label: o.label,
              index,
            };
          }
          options_data[index] = {
            value: o.value,
            label: o.label,
            index,
          };
        });
        input = (
          <div className="wcsn-form-select-field-wrapper">
            <Select
              className={key}
              value={value ? value : ""}
              options={options_data}
              onChange={(e) => {
                this.onChange(e, m.key, "single", type, options_data);
              }}
            ></Select>
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "mailchimp_select") {
        const options_data = [];
        const defaultselect = [];
        var selected_val = value;
        input = this.state.datamclist.map((o, index) => {
          if (o.selected) {
            defaultselect[index] = {
              value: o.value,
              label: o.label,
              index,
            };
          }
          options_data[index] = {
            value: o.value,
            label: o.label,
            index,
          };
        });
        input = (
          <div className="wcsn-form-select-field-wrapper">
            <Select
              className={key}
              value={selected_val ? selected_val : ""}
              options={options_data}
              onChange={(e) => {
                this.onChange(e, m.key, "single", type, options_data);
              }}
            ></Select>
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "button") {
        input = (
          <div className="wcsn-button">
            <input
              className="btn default-btn"
              type="button"
              value="Connect to Mailchimp"
              onClick={(e) => this.handleGetMailchimpList()}
            />
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{
                  __html: m.desc,
                }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "text_api") {
        input = (
          <div className="wcsn-settings-basic-input-class">
            <input
              {...props}
              className="wcsn-setting-form-input"
              type={type}
              key={key}
              id={m.id}
              placeholder={placeholder}
              name={name}
              value={value}
              onChange={(e) => {
                this.onChange(e, target, "single", type);
              }}
            />
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "checkbox") {
        input = (
          <div
            className={
              m.right_content
                ? "wcsn-checkbox-list-side-by-side"
                : m.parent_class
                ? "wcsn-checkbox-list-side-by-side"
                : ""
            }
          >
            {m.select_deselect ? (
              <div
                className="wcsn-select-deselect-trigger"
                onClick={(e) => {
                  this.onSelectDeselectChange(e, m);
                }}
              >
                Select / Deselect All
              </div>
            ) : (
              ""
            )}
            {m.options.map((o) => {
              //let checked = o.value === value;
              let checked = false;
              if (value && value.length > 0) {
                checked = value.indexOf(o.value) > -1 ? true : false;
              }
              return (
                <div
                  className={
                    m.right_content
                      ? "wcsn-toggle-checkbox-header"
                      : m.parent_class
                      ? m.parent_class
                      : ""
                  }
                >
                  <React.Fragment key={"cfr" + o.key}>
                    {m.right_content ? (
                      <p
                        className="wcsn-settings-metabox-description"
                        dangerouslySetInnerHTML={{
                          __html: o.label,
                        }}
                      ></p>
                    ) : (
                      ""
                    )}
                    <div className="wcsn-toggle-checkbox-content">
                      <input
                        {...props}
                        className={m.class}
                        type={type}
                        id={`wcsn-toggle-switch-${o.key}`}
                        key={o.key}
                        name={o.name}
                        checked={checked}
                        value={o.value}
                        onChange={(e) => {
                          this.onChange(e, m.key, "multiple");
                        }}
                      />
                      <label htmlFor={`wcsn-toggle-switch-${o.key}`}></label>
                    </div>
                    {m.right_content ? (
                      ""
                    ) : (
                      <p
                        className="wcsn-settings-metabox-description"
                        dangerouslySetInnerHTML={{
                          __html: o.label,
                        }}
                      ></p>
                    )}
                    {o.hints ? (
                      <span className="dashicons dashicons-info">
                        <div className="wcsn-hover-tooltip">{o.hints}</div>
                      </span>
                    ) : (
                      ""
                    )}
                  </React.Fragment>
                </div>
              );
            })}
            {m.desc ? (
              <p
                className="wcsn-settings-metabox-description"
                dangerouslySetInnerHTML={{ __html: m.desc }}
              ></p>
            ) : (
              ""
            )}
          </div>
        );
      }

      if (type === "example_form") {
        input = (
          <div className="wcsn-settings-example-button-class">
            {
              <div class="example_form_view">
                <div
                  class="example_form_alert_text"
                  style={{
                    color: this.state.form_description_text_color,
                  }}
                >
                  {this.state.form_description_text
                    ? this.state.form_description_text
                    : wcsnLocalizer.default_form_description_text}
                </div>
                <div class="example_form">
                  <div class="example_form_email">
                    <input
                      type="text"
                      value={
                        this.state.email_placeholder_text
                          ? this.state.email_placeholder_text
                          : wcsnLocalizer.default_email_place
                      }
                      readOnly
                    />
                  </div>
                  <div
                    className="example_alert_button"
                    onMouseEnter={this.handleMouseEnter}
                    onMouseLeave={this.handleMouseLeave}
                    style={{
                      color:
                        this.state.hover_on && this.state.subscribe_button_text_color_onhover
                          ? this.state.subscribe_button_text_color_onhover
                          : this.state.subscribe_button_text_color,
                      fontSize: this.state.subscribe_button_font_size + "px",
                      borderRadius: this.state.subscribe_button_border_radious + "px",
                      border: `${this.state.subscribe_button_border_size}px solid ${
                        this.state.hover_on &&
                        this.state.subscribe_button_border_color_onhover
                          ? this.state.subscribe_button_border_color_onhover
                          : this.state.subscribe_button_border_color
                      }`,

                      background:
                        this.state.hover_on &&
                        this.state.subscribe_button_background_color_onhover
                          ? this.state.subscribe_button_background_color_onhover
                          : this.state.subscribe_button_background_color,
                      verticalAlign: "middle",
                      textDecoration: "none",
                      width: "fit-content",
                    }}
                  >
                    {this.state.subscribe_button_text
                      ? this.state.subscribe_button_text
                      : wcsnLocalizer.default_subscribe_button_text}
                  </div>
                </div>
              </div>
            }
          </div>
        );
      }

      return m.type === "section" || m.label === "no_label" ? (
        input
      ) : (
        <div key={"g" + key} className="wcsn-form-group">
          <label
            className="wcsn-settings-form-label"
            key={"l" + key}
            htmlFor={key}
          >
            <p dangerouslySetInnerHTML={{ __html: m.label }}></p>
          </label>
          <div className="wcsn-settings-input-content">{input}</div>
        </div>
      );
    });
    return formUI;
  };

  render() {
    return (
      <div className="wcsn-dynamic-fields-wrapper">
        {this.state.errordisplay ? (
          <div className="wcsn-notice-display-title">
            <i className="wcsn-stock-notifier icon-success-notification"></i>
            {this.state.errordisplay}
          </div>
        ) : (
          ""
        )}

        <form
          className="wcsn-dynamic-form"
          onSubmit={(e) => {
            this.onSubmit(e);
          }}
        >
          <div className="wcsn-submit-form">
            <input type="submit" value="Save" class="wcsn-button submit-btn " />
          </div>
          {this.renderForm()}
        </form>
      </div>
    );
  }
}
