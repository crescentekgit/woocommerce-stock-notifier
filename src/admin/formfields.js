import React, { useState, useEffect } from "react";
import Select from "react-select";
import axios from "axios";

const FormFields = (props) => {
  const [state, setState] = useState({
    open_model: false,
    datamclist: [],
    from_loading: false,
    errordisplay: ""
  });

  const useQuery = () => {
    return new URLSearchParams(useLocation().hash);
  };

  const handleDragEnd = () => {
    if (props.submitbutton && props.submitbutton === "false") {
      setTimeout(() => {
        onSubmit("");
      }, 10);
    }
  };

  const handleOnChangerange = (e, target) => {
    setState((prevState) => ({
      ...prevState,
      subscribe_button_font_size:
        target === 'subscribe_button_font_size'
          ? e.target.value
          : prevState.subscribe_button_font_size,
      subscribe_button_border_radious:
        target === 'subscribe_button_border_radious'
          ? e.target.value
          : prevState.subscribe_button_border_radious,
      subscribe_button_border_size:
        target === 'subscribe_button_border_size'
          ? e.target.value
          : prevState.subscribe_button_border_size,
    }));
  };

  const handleMouseEnter = () => {
    setState((prevState) => ({
      ...prevState,
      hover_on: true,
    }));
  };

  const handleMouseLeave = () => {
    setState((prevState) => ({
      ...prevState,
      hover_on: false,
    }));
  };

  const handleGetButtonColorState = () => {
    axios
      .get(`${wcsnLocalizer.apiUrl}/wc_stocknotifier/v1/get_button_data`)
      .then((response) => {
        setState((prevState) => ({
          ...prevState,
          form_description_text_color: response.data.form_description_text_color,
          subscribe_button_background_color:
            response.data.subscribe_button_background_color,
          subscribe_button_border_color:
            response.data.subscribe_button_border_color,
          subscribe_button_text_color: response.data.subscribe_button_text_color,
          subscribe_button_background_color_onhover:
            response.data.subscribe_button_background_color_onhover,
          subscribe_button_border_color_onhover:
            response.data.subscribe_button_border_color_onhover,
          subscribe_button_text_color_onhover:
            response.data.subscribe_button_text_color_onhover,
          subscribe_button_font_size: response.data.subscribe_button_font_size,
          subscribe_button_border_radious:
            response.data.subscribe_button_border_radious,
          subscribe_button_border_size: response.data.subscribe_button_border_size,
        }));
      });
  };

  const handleOnChangeColor = (e, target) => {
    setState((prevState) => ({
      ...prevState,
      form_description_text_color:
        target === "form_description_text_color"
          ? e.target.value
          : prevState.form_description_text_color,
      subscribe_button_text_color:
        target === "subscribe_button_text_color"
          ? e.target.value
          : prevState.subscribe_button_text_color,
      subscribe_button_background_color:
        target === "subscribe_button_background_color"
          ? e.target.value
          : prevState.subscribe_button_background_color,
      subscribe_button_border_color:
        target === "subscribe_button_border_color"
          ? e.target.value
          : prevState.subscribe_button_border_color,
      subscribe_button_background_color_onhover:
        target === "subscribe_button_background_color_onhover"
          ? e.target.value
          : prevState.subscribe_button_background_color_onhover,
      subscribe_button_border_color_onhover:
        target === "subscribe_button_border_color_onhover"
          ? e.target.value
          : prevState.subscribe_button_border_color_onhover,
      subscribe_button_text_color_onhover:
        target === "subscribe_button_text_color_onhover"
          ? e.target.value
          : prevState.subscribe_button_text_color_onhover,
    }));

    if (props.submitbutton && props.submitbutton === "false") {
      setTimeout(() => {
        onSubmit("");
      }, 10);
    }
  };

  const handleGetMailchimpList = () => {
    // implement logic for getting mailchimp list
  };

  const onSubmit = (e) => {
    const prop_submitbutton =
      props.submitbutton && props.submitbutton === "false" ? "" : "true";
    if (prop_submitbutton) {
      e.preventDefault();
    }
    setState((prevState) => ({
      ...prevState,
      from_loading: true,
    }));

    axios({
      method: props.method,
      url: `${wcsnLocalizer.apiUrl}/${props.url}`,
      headers: { 'X-WP-Nonce' : wcsnLocalizer.nonce },
      data: {
        model: state,
        modulename: props.modulename,
      },
    }).then((res) => {
      setState((prevState) => ({
        ...prevState,
        from_loading: false,
        errordisplay: res.data.error,
      }));
      setTimeout(() => {
        setState((prevState) => ({
          ...prevState,
          errordisplay: "",
        }));
      }, 2000);
      if (res.data.redirect_link) {
        window.location.href = res.data.redirect_link;
      }
    });
  };

  useEffect(() => {
    if (props.modulename === "form_personalize") {
      handleGetButtonColorState();
    }

    // Fetch all datas
    props.model.map((m) => {
      setState((prevState) => ({
        ...prevState,
        [m.key]: m.database_value,
      }));
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
  }, []); // Empty dependency array to run once on mount

  const onChange = (e, key, type = "single", from_type = "", array_values = []) => {
    if (type === "single") {
      if (from_type === "select") {
        setState((prevState) => ({
          ...prevState,
          [key]: array_values[e.index],
        }));
      } else if (from_type === "mailchimp_select") {
        setState((prevState) => ({
          ...prevState,
          [key]: array_values[e.index],
        }));
      } else if (from_type === "multi-select") {
        setState((prevState) => ({
          ...prevState,
          [key]: e,
        }));
      } else if (from_type === "text_api") {
        setState((prevState) => ({
          ...prevState,
          [key]: e.target.value,
        }));
        setState((prevState) => ({
          ...prevState,
          datamclist: [],
        }));
        setState((prevState) => ({
          ...prevState,
          selected_mailchimp_list: "",
        }));
      } else if (from_type === 'checkbox') {
        setState((prevState) => ({
          ...prevState, 
          [key]: e.target.checked 
        }));
       } else {
        setState((prevState) => ({
          ...prevState,
          [key]: e.target.value,
        }));
      }
    } else {
      // Array of values (e.g., checkbox): TODO: Optimization needed.
      const found = state[key]
        ? state[key].find((d) => d === e.target.value)
        : false;

      if (found) {
        const data = state[key].filter((d) => {
          return d !== found;
        });
        setState((prevState) => ({
          ...prevState,
          [key]: data,
        }));
      } else {
        const others = state[key] ? [...state[key]] : [];
        setState((prevState) => ({
          ...prevState,
          [key]: [e.target.value, ...others],
        }));
      }
    }
    if (props.submitbutton && props.submitbutton === "false") {
      if (key !== "password") {
        setTimeout(() => {
          onSubmit("");
        }, 10);
      }
    }
  };

  const renderForm = () => {
    const model = props.model;
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

      value = state[target] || "";

      if (m.restricted_page && m.restricted_page === useQuery().get("tab")) {
        return false;
      }

      // If no array key found
      if (!m.key) {
        return false;
      }

      // for select selection
      if (
        m.depend &&
        state[m.depend] &&
        state[m.depend].value &&
        state[m.depend].value !== m.dependvalue
      ) {
        return false;
      }

      // for radio button selection
      if (
        m.depend &&
        state[m.depend] &&
        !state[m.depend].value &&
        state[m.depend] !== m.dependvalue
      ) {
        return false;
      }

      // for checkbox selection
      if (
        m.depend_checkbox &&
        state[m.depend_checkbox] &&
        state[m.depend_checkbox].length === 0
      ) {
        return false;
      }

      // for checkbox selection
      if (
        m.not_depend_checkbox &&
        state[m.not_depend_checkbox] &&
        state[m.not_depend_checkbox].length > 0
      ) {
        return false;
      }

      if (m.depend && !state[m.depend]) {
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
                onChange(e, target);
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
                    handleOnChangeColor(e, "form_description_text_color");
                  }}
                  value={state.form_description_text_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.submit_button_text}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    handleOnChangeColor(e, "subscribe_button_text_color");
                  }}
                  value={state.subscribe_button_text_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.background}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    handleOnChangeColor(e, "subscribe_button_background_color");
                  }}
                  value={state.subscribe_button_background_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.border}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    handleOnChangeColor(e, "subscribe_button_border_color");
                  }}
                  value={state.subscribe_button_border_color}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.hover_background}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    handleOnChangeColor(
                      e,
                      "subscribe_button_background_color_onhover"
                    );
                  }}
                  value={state.subscribe_button_background_color_onhover}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.hover_border}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    handleOnChangeColor(e, "subscribe_button_border_color_onhover");
                  }}
                  value={state.subscribe_button_border_color_onhover}
                />
              </div>
              <div className="wcsn-color-picker-wrap">
                {wcsnLocalizer.setting_string.hover_text}
                <input
                  {...props}
                  className="wcsn-setting-color-picker"
                  type="color"
                  onChange={(e) => {
                    handleOnChangeColor(e, "subscribe_button_text_color_onhover");
                  }}
                  value={state.subscribe_button_text_color_onhover}
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
                    value={state.subscribe_button_font_size}
                    onChange={(e) => {
                      handleOnChangerange(e, "subscribe_button_font_size");
                    }}
                    onMouseUp={handleDragEnd}
                    onTouchEnd={handleDragEnd}
                  />
                  <output class="bubble">
                    {state.subscribe_button_font_size
                      ? state.subscribe_button_font_size
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
                    value={state.subscribe_button_border_radious}
                    onChange={(e) => {
                      handleOnChangerange(e, "subscribe_button_border_radious");
                    }}
                    onMouseUp={handleDragEnd}
                    onTouchEnd={handleDragEnd}
                  />
                  <output class="bubble">
                    {state.subscribe_button_border_radious
                      ? state.subscribe_button_border_radious
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
                    value={state.subscribe_button_border_size}
                    onChange={(e) => {
                      handleOnChangerange(e, "subscribe_button_border_size");
                    }}
                    onMouseUp={handleDragEnd}
                    onTouchEnd={handleDragEnd}
                  />
                  <output class="bubble">
                    {state.subscribe_button_border_size
                      ? state.subscribe_button_border_size
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
                onChange(e, target);
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

      if (type === "mailchimp_select") {
        input = (
          <Select
            {...props}
            value={state[key]}
            onChange={(e) => {
              onChange(e, key, "mailchimp_select", "", e.options);
            }}
            options={state.datamclist}
            placeholder={placeholder}
            className="react-select-container"
            classNamePrefix="react-select"
          />
        );
      }

      if (type === "select") {
        input = (
          <select
            {...props}
            value={state[key]}
            onChange={(e) => {
              onChange(e, key, "select", "", e.options);
            }}
            className="wcsn-setting-form-select"
          >
            <option value="" disabled>
              {placeholder}
            </option>
            {props.options &&
              props.options.map((option, i) => (
                <option key={i} value={option.value}>
                  {option.label}
                </option>
              ))}
          </select>
        );
      }

      if (type === "textarea") {
        input = (
          <div className="wcsn-setting-from-textarea">
            <textarea
              {...props}
              className={m.class ? m.class : "wcsn-setting-wpeditor-class"}
              key={key}
              id={m.id}
              placeholder={placeholder}
              name={name}
              value={value}
              onChange={(e) => {
                onChange(e, target);
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
                          onChange(e, target, 'checkbox');
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

      if (type === "radio") {
        input = (
          <div className="wcsn-settings-basic-input-class wcsn-radio-wrapper">
            {props.options &&
              props.options.map((option, i) => (
                <div key={i} className="wcsn-radio-option">
                  <input
                    {...props}
                    className="wcsn-setting-form-radio"
                    type={type}
                    key={key}
                    id={m.id + i}
                    name={name}
                    value={option.value}
                    checked={value === option.value}
                    onChange={(e) => {
                      onChange(e, target);
                    }}
                  />
                  <label htmlFor={m.id + i} className="wcsn-settings-radio-label">
                    {option.label}
                  </label>
                </div>
              ))}
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
                    color: state.form_description_text_color,
                  }}
                >
                  {state.form_description_text
                    ? state.form_description_text
                    : wcsnLocalizer.default_form_description_text}
                </div>
                <div class="example_form">
                  <div class="example_form_email">
                    <input
                      type="text"
                      value={
                        state.email_placeholder_text
                          ? state.email_placeholder_text
                          : wcsnLocalizer.default_email_place
                      }
                      readOnly
                    />
                  </div>
                  <div
                    className="example_alert_button"
                    onMouseEnter={handleMouseEnter}
                    onMouseLeave={handleMouseLeave}
                    style={{
                      color:
                        state.hover_on && state.subscribe_button_text_color_onhover
                          ? state.subscribe_button_text_color_onhover
                          : state.subscribe_button_text_color,
                      fontSize: state.subscribe_button_font_size + "px",
                      borderRadius: state.subscribe_button_border_radious + "px",
                      border: `${state.subscribe_button_border_size}px solid ${
                        state.hover_on &&
                        state.subscribe_button_border_color_onhover
                          ? state.subscribe_button_border_color_onhover
                          : state.subscribe_button_border_color
                      }`,

                      background:
                        state.hover_on &&
                        state.subscribe_button_background_color_onhover
                          ? state.subscribe_button_background_color_onhover
                          : state.subscribe_button_background_color,
                      verticalAlign: "middle",
                      textDecoration: "none",
                      width: "fit-content",
                    }}
                  >
                    {state.subscribe_button_text
                      ? state.subscribe_button_text
                      : wcsnLocalizer.default_subscribe_button_text}
                  </div>
                </div>
              </div>
            }
          </div>
        );
      }

      return (
        m.type === "section" || m.label === "no_label" || m.type === "customize_table" ? (
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
        )
      );
    });
    return formUI;
  };

  return (
    <div className="wcsn-dynamic-fields-wrapper">
        {state.errordisplay ? (
          <div className="wcsn-notice-display-title">
            <i className="wcsn-stock-notifier icon-success-notification"></i>
            {state.errordisplay}
          </div>
        ) : (
          ""
        )}
      <form
        onSubmit={(e) => {
          onSubmit(e);
        }}
        className="wcsn-dynamic-form"
      >
        <div className="wcsn-submit-form">
          <input type="submit" value="Save" class="wcsn-button submit-btn " />
        </div>
        {renderForm()}
      </form>
    </div>
  );
};

export default FormFields;
