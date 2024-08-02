(function ( wp ) {
  var el = wp.element.createElement;
  var addFilter = wp.hooks.addFilter;
    const {
    registerFormatType,
  } = wp.richText;
  const {
    BlockControls,
  } = wp.blockEditor;
  const {
    ToolbarGroup,
    ToolbarButton,
  } = wp.components;
  const {
    Button,
    Popover,
    SelectControl,
    TextControl
  } = wp.components;
  const {
    useState,
    useEffect
  } = wp.element;
  const FieldTypes = [{
    value: '',
    label: 'Select....'
  },{
    value: 'post_custom_fields',
    label: 'Post Custom Fields'
  }, {
    value: 'user_custom_fields',
    label: 'User Custom Fields'
  }, 
  // {
  //   value: 'taxonomy_custom_fields',
  //   label: 'Taxonomy Custom Fields'
  // }, {
  //   value: 'settings_custom_fields',
  //   label: 'Settings Custom Fields'
  // }
  ];
  // Define your custom icon
  var websiteURL = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
  var toolbarIcon = el('img', { src: websiteURL+'/wp-content/plugins/cubewp-framework/cube/assets/admin/images/dynamic.png', alt: 'toolbar-image' });
  //const textBlockTypes = ['core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/preformatted', 'core/verse', 'core/pullquote', 'core/button'];
  
  const MyCustomButton = ({
    isActive,
    onChange,
    value
  }) => {
    const defaultValue = 'Current Source';
    const postId = wp.data.select('core/editor').getCurrentPostId();
    const [isOpen, setOpen] = useState(false);
    const [isDisabled, setIsDisabled] = useState(true);
    const [fType, setfType] = useState('');
    const [fSource, setfSource] = useState('');
    const [cSource, setcSource] = useState('Current Source');
    const [crSource, setcSourceForRender] = useState(postId);
    const [fName, setfName] = useState('');

    const [searchResults, setSearchResults] = useState([]);
    var debounceTimeout;

    const [FieldOptions, setOptions] = useState([]);
    const [FieldValue, setValue] = useState('');
    
    const [showSourceSelect, setshowSourceSelect] = useState(false);
    const [showContentSelect, setshowContentSelect] = useState(false);
    const [showFieldsSelect, setShowFieldsSelect] = useState(false);

    
    const togglePopover = () => {
      setOpen(!isOpen);
    };
    const onTypeChange = value => {
      setfSource('');
      setfName('');
      setfType(value);
      if(value == 'taxonomy_custom_fields'){
        setShowFieldsSelect(!!value);
        setshowContentSelect(false);
      }else{
        setshowSourceSelect(!!value);
      }
    };
    const onSourceChange = value => {
      setfSource(value);
      setshowContentSelect(!!value);
      setShowFieldsSelect(!!value);
    };
    const onContentChange = value => {
      setcSource(value);
    };
    const onFieldChange = value => {
      setIsDisabled(true);
      setfName(value);
    };

    const buttonClicked = () => {
      let contentSource = crSource;
      if(crSource === postId){
        contentSource = 'current-source';
      }
      addContent(FieldValue,fType,fSource,fName,contentSource);
      setOpen(!isOpen);
    };
    
    const getSourceOptions = () => {
      // Define the options for the second select control based on the selected value of the first select control
      switch (fType) {
        case 'post_custom_fields':
          const postTypes = cubewp_block_params.cf_post_types;
          const postTypesEntries = Object.entries(postTypes);
          const PostTypesResult = [];
          for (let [key, value] of postTypesEntries) {
            var SingleType = {
              label: value,
              value: key
            };
            PostTypesResult.push(SingleType);
          }
          return PostTypesResult;
        case 'user_custom_fields':
          const UserRoles = cubewp_block_params.cf_user_roles;
          const UserRolesEntries = Object.entries(UserRoles);
          const UserRolesResult = [];
          for (let [key, value] of UserRolesEntries) {
            var SingleRole = {
              label: value,
              value: key
            };
            UserRolesResult.push(SingleRole);
          }
          return UserRolesResult;
  
        default:
          return [];
      }
    };

    const FieldsInputType = ['text','number','dropdown','checkbox','radio','textarea'];

    useEffect(() => {
      getCubeWPCustomFields(fType, fSource, FieldsInputType)
        .then(responseOptions => {
          setOptions(responseOptions);
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }, [fType, fSource], FieldsInputType);

    useEffect(() => {
      renderCubeWPCustomField(fType, fName, crSource)
        .then(responseOptions => {
          setValue(responseOptions);
          setIsDisabled(!isDisabled);
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }, [fType, fName, crSource]);
    
    useEffect(function () {
      var search = async function () {
        var results = await fetchSearchResults(fType,cSource,fSource);
        setSearchResults(results);
      };
      if (debounceTimeout) {
        clearTimeout(debounceTimeout);
      }
      debounceTimeout = setTimeout(search, 500);
      return function cleanup() {
        if (debounceTimeout) {
          clearTimeout(debounceTimeout);
        }
      };
    }, [cSource]);
    var handleSearchResultClick = function (result) {
      setcSourceForRender(result.id);
      setcSource(result.title.rendered);
      setSearchResults([]);
    };
    var handleClearClick = function () {
      setcSource(defaultValue);
      setcSourceForRender(postId);
      setSearchResults([]);
    };
    return el(
            BlockControls,
            { key: 'controls' },
            el("div", {
            className: "components-toolbar"
          },
                      
          el(ToolbarButton, {
            label: "Select Dynamic Data",
            icon: toolbarIcon,
            onClick: togglePopover,
            isPressed: isOpen,
            isFocus: false,
            className: "cubewp-toolbar-control for-text"
          }), 
          
          isOpen && el(Popover, {
            position: "bottom right",
            onClose: togglePopover
          },
          
          el("div", {
            className: "cubewp-cf-popover-inner-wrap",
          },
          
          el("div", {
            className: "cubewp-cf-select1-inner-wrap",
          },
          
          el(SelectControl, {
            label: "Custom Fields Type",
            value: fType,
            options: FieldTypes,
            onChange: onTypeChange
          })),
          
          el("div", {
            className: "cubewp-cf-select2-inner-wrap",
          }, 
          
          showSourceSelect && el(SelectControl, {
            label: "Custom Fields Source",
            value: fSource,
            options: getSourceOptions(),
            onChange: onSourceChange,
            className: "my-custom-block-select2 custom-css-class",
          })),

          el("div", {
            className: "cubewp-cf-select3-inner-wrap",
          }, 
          
          showContentSelect && el(TextControl, {
            label: "Content Source",
            value: cSource,
            onChange: onContentChange,
            onFocus: function onFocus() {
              if (cSource === defaultValue) {
                setcSource('');
              }
            },
            onBlur: function onBlur() {
              if (cSource === '') {
                setcSource(defaultValue);
              }
            }
          }), searchResults.length > 0 && el('ul', null, searchResults.map(function (result) {
            var title = '';
            if (typeof result.title === 'string') {
              title = result.title;
            }else{
              title = result.title.rendered;
            }
            return el('li', {
              key: result.id,
              'data-id': result.id,
              onClick: function () {
                return handleSearchResultClick(result);
              }
            }, title);
          })),
            cSource !== defaultValue && el('button', {
            className: 'clear-button',
            onClick: handleClearClick
          }, 'Clear')
          ),

          el("div", {
            className: "cubewp-cf-select4-inner-wrap",
          }, 
          
          showFieldsSelect && el(SelectControl, {
            label: "Custom Field Name",
            value: fName,
            options: FieldOptions,
            onChange: onFieldChange,
            className: "my-custom-block-select4",
          })),

          el(Button, {
            className: "is-primary",
            disabled: isDisabled,
            onClick: buttonClicked,
          }, "Add Custom Field")
          
          )),
      )
    );
  };
  function getCubeWPCustomFields(fType, fSource, FieldsInputType) {
    const NameSpace = '/cubewp-custom-fields/v1';
    const FieldsEndPoint = '/custom_fields';
    const Fieldstype = '?fields_type=' + fType;
    const FieldsSource = '&fields_source=' + fSource;
    const FieldsInput = '&fields_input_type=' + FieldsInputType;
    const FieldsResult = [];
    return wp.api.loadPromise.then(() => {
      return wp.apiRequest({ path: NameSpace + FieldsEndPoint + Fieldstype + FieldsSource + FieldsInput })
        .then(response => {
          const FieldsEntries = Object.entries(response);
          for (let [key, value] of FieldsEntries) {
            FieldsResult.push({
              label: value,
              value: key
            });
          }
          return FieldsResult;
        });
    });
  }
  function renderCubeWPCustomField(fType, field, pID) {
    return new Promise((resolve, reject) => {
      if (field !== '' && pID !== '') {
        const NameSpace = '/cubewp-custom-fields/v1';
        const FieldsEndPoint = '/render';
        const FieldType = '?fields_type=' + fType;
        const FieldName = '&field_name=' + field;
        const postID = '&post_id=' + pID;
  
        wp.api.loadPromise.then(() => {
          wp.apiRequest({ path: NameSpace + FieldsEndPoint + FieldType + FieldName + postID })
            .then(response => {
              resolve(response);
            })
            .catch(error => {
              reject(error);
            });
        });
      } else {
        resolve([]);
      }
    });
  }
  var fetchSearchResults = async function (fType, searchTerm, type) {
    var data = [];
  
    if (searchTerm !== '') {
      if (fType === 'post_custom_fields') {
        var response = await fetch(`/wp-json/wp/v2/${type}?search=` + encodeURIComponent(searchTerm));
        data = await response.json();
      } else if (fType === 'user_custom_fields') {
        var response = await fetch(`/wp-json/wp/v2/search?search=` + encodeURIComponent(searchTerm) + '&per_page=20&type=post&_locale=user');
        data = await response.json();
      }
    }
  
    return data;
  };
  
  function addContent(content, select1, select2, select3, contentSource) {
    var blockEditor = wp.data.select('core/block-editor');
    var dispatch = wp.data.dispatch('core/block-editor');

    if (!blockEditor) {
        console.error("Block editor not found.");
        return;
    }

    var currentBlockClientId = blockEditor.getSelectedBlockClientId();
    if (!currentBlockClientId) {
        console.error("No block is selected.");
        return;
    }

    var currentBlock = blockEditor.getBlock(currentBlockClientId);
    if (!currentBlock) {
        console.error("Block not found.");
        return;
    }

    var contentEditable = document.querySelector(`.block-editor-block-list__layout [data-block="${currentBlockClientId}"]`);
    if (!contentEditable) {
        console.error("Content editable element not found.");
        return;
    }

    // Remove the existing span with the placeholder
    var existingNode = contentEditable.querySelector('[data-rich-text-placeholder]');
    if (existingNode) {
        existingNode.parentNode.removeChild(existingNode);
    }

    // Create new node as HTML string
    var newNodeHTML = `<span data-type="${select1}" data-source="${select2}" data-content-source="${contentSource}" data-name="${select3}" class="cwp-dynamic-field">${content}</span>`;

    // Get current block content
    var currentContent = blockEditor.getBlockAttributes(currentBlockClientId).content || '';

    // Append new node HTML to current content
    var updatedContent = currentContent + newNodeHTML;

    // Update the block content using dispatch
    dispatch.updateBlockAttributes(currentBlockClientId, { content: updatedContent });
  }


  registerFormatType('my-custom-format/sample-output', {
    title: 'CubeWP Custom Fields',
    tagName: 'custom-fields',
    className: 'cubewp-custom-fields',
    edit: MyCustomButton
  });
  addFilter('editor.BlockEdit', 'my-plugin/image-block-edit', 
    function (BlockEdit) {
      return function (props) {
        const {
          name,
          attributes,
          setAttributes
        } = props;
        const {
          url,
          alt,
          caption,
          images
        } = attributes;
        
        if (name === 'core/image' || name === 'core/gallery' || name === 'kadence/image') {
          const defaultValue = 'Current Source';
          const postId = wp.data.select('core/editor').getCurrentPostId();
          const [isOpen, setOpen] = useState(false);
          const [isDisabled, setIsDisabled] = useState(true);
          const [fType, setfType] = useState('');
          const [fSource, setfSource] = useState('');
          const [cSource, setcSource] = useState('Current Source');
          const [crSource, setcSourceForRender] = useState(postId);
          const [fName, setfName] = useState('');

          const [searchResults, setSearchResults] = useState([]);
          var debounceTimeout;

          const [FieldOptions, setOptions] = useState([]);
          const [FieldValue, setValue] = useState('');
          
          const [showSourceSelect, setshowSourceSelect] = useState(false);
          const [showContentSelect, setshowContentSelect] = useState(false);
          const [showFieldsSelect, setShowFieldsSelect] = useState(false);

          
          const togglePopover = () => {
            setOpen(!isOpen);
          };
          const onTypeChange = value => {
            setfSource('');
            setfName('');
            setfType(value);
            if(value == 'taxonomy_custom_fields'){
              setShowFieldsSelect(!!value);
              setshowContentSelect(false);
            }else{
              setshowSourceSelect(!!value);
            }
          };
          const onSourceChange = value => {
            setfSource(value);
            setshowContentSelect(!!value);
            setShowFieldsSelect(!!value);
          };
          const onContentChange = value => {
            setcSource(value);
          };
          const onFieldChange = value => {
            setIsDisabled(true);
            setfName(value);
          };

          const buttonClicked = () => {
            
            let contentSource = crSource;
            if(crSource === postId){
              contentSource = 'current-source';
            }
            if (name === 'core/gallery') {
              const attachmentIds = FieldValue;
          
              async function fetchImageDetails(id) {
                  const response = await fetch(`/wp-json/wp/v2/media/${id}`);
                  const data = await response.json();
                  
                  return {
                      id: data.id,
                      url: data.source_url,
                      alt: data.alt_text || 'default alt text',  // Add a default alt text if not available
                      sizeSlug: 'large',  // You can modify this to use actual size slugs if needed
                      linkDestination: 'none',  // Assuming 'none' as the default link destination
                  };
              }
          
              async function fetchAllImageDetails(ids) {
                  const promises = ids.map(id => fetchImageDetails(id));
                  const images = await Promise.all(promises);
                  return images;
              }
          
              fetchAllImageDetails(attachmentIds).then(images => {
                  const galleryBlock = wp.data.select('core/block-editor').getBlock(props.clientId);
                  if (galleryBlock) {
                      const imageBlocks = images.map((image, index) => {
                        const imageUrl = `${image.url}?data-type=${fType}&data-source=${fSource}&data-content-source=${contentSource}&data-name=${fName}`;
                          const imageAttributes = {
                              url: imageUrl,
                              alt: image.alt,
                              sizeSlug: image.sizeSlug,
                              linkDestination: image.linkDestination,
                              id: `${index}_image`,
                              
                          };
                          console.log(imageUrl);
                          return wp.blocks.createBlock('core/image', imageAttributes);
                      });
                      imageBlocks.forEach(imageBlock => {
                          wp.data.dispatch('core/editor').insertBlock(imageBlock, galleryBlock.innerBlocks.length, props.clientId);
                      });
                  }
              }).catch(error => {
                  console.error('Error fetching image details:', error);
              });
          }else if (name === 'core/image'){
              setAttributes({
                url: `https://imgv3.fotor.com/images/cover-photo-image/a-beautiful-girl-with-gray-hair-and-lucxy-neckless-generated-by-Fotor-AI.jpg?data-type=${fType}&data-source=${fSource}&data-content-source=${contentSource}&data-name=${fName}`,
                alt: 'alt hai ye',
              });
            }
            
            setOpen(!isOpen);
          };
        
          const getSourceOptions = () => {
            // Define the options for the second select control based on the selected value of the first select control
            switch (fType) {
              case 'post_custom_fields':
                const postTypes = cubewp_block_params.cf_post_types;
                const postTypesEntries = Object.entries(postTypes);
                const PostTypesResult = [];
                for (let [key, value] of postTypesEntries) {
                  var SingleType = {
                    label: value,
                    value: key
                  };
                  PostTypesResult.push(SingleType);
                }
                return PostTypesResult;
              case 'user_custom_fields':
                const UserRoles = cubewp_block_params.cf_user_roles;
                const UserRolesEntries = Object.entries(UserRoles);
                const UserRolesResult = [];
                for (let [key, value] of UserRolesEntries) {
                  var SingleRole = {
                    label: value,
                    value: key
                  };
                  UserRolesResult.push(SingleRole);
                }
                return UserRolesResult;
        
              default:
                return [];
            }
          };

          if (name === 'core/gallery'){
            FieldsImageType = ['gallery'];
          }else{
            FieldsImageType = ['image'];
          }

          useEffect(() => {
            getCubeWPCustomFields(fType, fSource, FieldsImageType)
              .then(responseOptions => {
                setOptions(responseOptions);
              })
              .catch(error => {
                console.error('Error:', error);
              });
          }, [fType, fSource], FieldsImageType);

          useEffect(() => {
            renderCubeWPCustomField(fType, fName, crSource)
              .then(responseOptions => {
                setValue(responseOptions);
                setIsDisabled(!isDisabled);
              })
              .catch(error => {
                console.error('Error:', error);
              });
          }, [fType, fName, crSource]);
          
          useEffect(function () {
            var search = async function () {
              var results = await fetchSearchResults(fType,cSource,fSource);
              setSearchResults(results);
            };
            if (debounceTimeout) {
              clearTimeout(debounceTimeout);
            }
            debounceTimeout = setTimeout(search, 500);
            return function cleanup() {
              if (debounceTimeout) {
                clearTimeout(debounceTimeout);
              }
            };
          }, [cSource]);
          var handleSearchResultClick = function (result) {
            setcSourceForRender(result.id);
            setcSource(result.title.rendered);
            setSearchResults([]);
          };
          var handleClearClick = function () {
            setcSource(defaultValue);
            setcSourceForRender(postId);
            setSearchResults([]);
          };
          var BlockEditWithToolbar = el(BlockEdit, props);
          //var isTextBlock = textBlockTypes.includes(props.name);
      
          return el(
              wp.element.Fragment,
              {},
              BlockEditWithToolbar,
              el(
                  BlockControls,
                  { key: 'controls' },
                  el("div", {
                  className: "components-toolbar"
                },
                            
                el(ToolbarButton, {
                  label: "Select Dynamic Image",
                  icon: toolbarIcon,
                  onClick: togglePopover,
                  isPressed: isOpen,
                  isFocus: false,
                  className: "cubewp-toolbar-control for-image"
                }), 
                
                isOpen && el(Popover, {
                  position: "bottom right",
                  onClose: togglePopover
                },
                
                el("div", {
                  className: "cubewp-cf-popover-inner-wrap",
                },
                
                el("div", {
                  className: "cubewp-cf-select1-inner-wrap",
                },
                
                el(SelectControl, {
                  label: "Custom Fields Type",
                  value: fType,
                  options: FieldTypes,
                  onChange: onTypeChange
                })),
                
                el("div", {
                  className: "cubewp-cf-select2-inner-wrap",
                }, 
                
                showSourceSelect && el(SelectControl, {
                  label: "Custom Fields Source",
                  value: fSource,
                  options: getSourceOptions(),
                  onChange: onSourceChange,
                  className: "my-custom-block-select2 custom-css-class",
                })),

                el("div", {
                  className: "cubewp-cf-select3-inner-wrap",
                }, 
                
                showContentSelect && el(TextControl, {
                  label: "Content Source",
                  value: cSource,
                  onChange: onContentChange,
                  onFocus: function onFocus() {
                    if (cSource === defaultValue) {
                      setcSource('');
                    }
                  },
                  onBlur: function onBlur() {
                    if (cSource === '') {
                      setcSource(defaultValue);
                    }
                  }
                }), searchResults.length > 0 && el('ul', null, searchResults.map(function (result) {
                  var title = '';
                  if (typeof result.title === 'string') {
                    title = result.title;
                  }else{
                    title = result.title.rendered;
                  }
                  return el('li', {
                    key: result.id,
                    'data-id': result.id,
                    onClick: function () {
                      return handleSearchResultClick(result);
                    }
                  }, title);
                })),
                  cSource !== defaultValue && el('button', {
                  className: 'clear-button',
                  onClick: handleClearClick
                }, 'Clear')
                ),

                el("div", {
                  className: "cubewp-cf-select4-inner-wrap",
                }, 
                
                showFieldsSelect && el(SelectControl, {
                  label: "Custom Field Name",
                  value: fName,
                  options: FieldOptions,
                  onChange: onFieldChange,
                  className: "my-custom-block-select4",
                })),

                el(Button, {
                  className: "is-primary",
                  disabled: isDisabled,
                  onClick: buttonClicked,
                }, "Add Custom Field")
                
                )),
                )
              )
          );
        };
        return /*#__PURE__*/React.createElement(BlockEdit, props);
      }
    }
    
    );

})(window.wp);