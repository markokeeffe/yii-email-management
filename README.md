yii-email-management
====================

An email templating and content management system.

## Installation ##

Add the following to the `require` object in your `composer.json`:

```json
  "require": {
    ...
    "markokeeffe/yii-email-management": "dev-master"
  },
```

Update composer:

```bash
$ composer update
```

Add the Emails application component to your config:

```php
  'Emails' => array(
    'class' => '\Veneficus\Email\Emails',
    'mappableObjects' => array(
      'Content',
    ),
    'imagePlaceholder' => 'http://www.placehold.it/{width}x{height}',
    'mailchimpKey' => '...',

    'subidModel' => 'Subid',
    'subidKey' => 'id',
    'subidVal' => 'value',
  ),
```

Add an 'emails' module:

```php
  'modules' => array(
    'emails' => array(
      'class' => 'vendor.markokeeffe.yii-email-management.src.EmailsModule',
      'injectContent' => array(
        'form' => '//update/_injectEmailContentForm',
        'model' => 'InjectEmailContentForm',
      ),
    ),
  ),
```

## Configuration ##

Configuration options for Emails component:

| Name | Example | Description |
| --- | --- | --- |
| `mappableObjects` | `['Content']` | \*Required\* The class names of objects in your system that can have their attributes mapped into email template layouts |
| `imagePlaceholder` | `http://placehold.it/{width}x{height}` | URL to an image placeholder API |
| `mailchimpKey` | `e3c3dceddcf06d37ba30334743035e58-u45` | A <a href="http://apidocs.mailchimp.com/" target="_blank">MailChimp</a> API key. The API is used to generate text emails. |
| `subidModel` | `Subid` | The name of a model that can be used to get 'subid' values. |
| `subidKey` | `id` | The attribute name for the primary key of the Subid model. |
| `subidVal` | `value` | The attribute name for the subid value of the Subid model. |

Configuration options for 'emails' module:

| Name | Example | Description |
| --- | --- | --- |
| `[injectContent][form]` | `'//update/_injectEmailContentForm'` | The path to a partial view file used to render a form that can be used to automatically populate an email's content depending on some form inputs e.g. an update date. |
| `[injectContent][model]` | `'InjectEmailContentForm'` | The name of a model class used to handle the injector form. |

## Accessing the Module ##

If you added the module to your config with the key `emails`, then the email management system will be available on the following URLs:

| URL | Description |
| --- | --- |
| /emails/template/index | Lists all email templates |
| /emails/template/create | Create a new email template |
| /emails/email/index | Lists all emails |
| /emails/email/create | Create a new email |

## Getting Started ##

In order to create emails, you first need email templates. Templates are HTML with some added tags and attributes to specify where content can be added. Browse to /emails/template/create, give the template a name, paste in the source code, and choose whether the order of content is fixed or not:

 - Fixed: The layouts within the `<repeater>` tag are fixed in position and their content can be changed.
 - Not Fixed: The layouts within the `<repeater>` tag are pulled out of the template source, and can be dynamically added back in to an email in any order.

See the next section for the template language, and how layouts can be specified.

Once a template has been created, you can create a new email from it. Browse to /emails/email/create, give the email a name, choose the template to use, and optionally specify a subject line, or Subid value. When you save, you will be forwarded to the email content editor. Click on a layout to edit it's content.

## Email Template Language ##

The email templates are HTML files with some extra markup to define layouts and editable tags within those layouts. This is based on the <a href="http://www.campaignmonitor.com/create/" target="_blank">Campaign Monitor templating language</a> with some improvements.

The basic principle is that standard HTML can be used to create an email template, some extra tags can be added to the HMTL to allow control over which parts of the HTML are used in an email and what content can be edited dynamically.

A template can be split into layouts by wrapping parts of the source in `<layout>` tags. These `<layout>` tags must be placed in a `<repeater>` tag to allow them to be stripped out of the original HTML template and dropped in dynamically.

Within a layout, content can be made editable using `<singleline>`, `<multiline>`, and `<img editable>` tags. Explained below:

| Name | Example | Description |
| --- | --- | --- |
| <a href="http://www.campaignmonitor.com/create/editable-content/#repeater">`<repeater>`</a> | `<repeater>`<br>`Layouts go here...`<br>`</repeater>` | A container for layouts. |
| <a href="http://www.campaignmonitor.com/create/editable-content/#layout">`<layout>`</a> | `<layout label="Single Column">`<br>`HTML for layout goes here...`<br>`</layout>` | A wrapper to contain markup and editable elements that can be dropped into an email, moved around and repeated. Identified by their label |
| <a href="http://www.campaignmonitor.com/create/editable-content/#singleline">`<singleline>`</a> | `<singleline label="Item Name">Text</singleline>` | A tag used to represent inline dynamic content |
| <a href="http://www.campaignmonitor.com/create/editable-content/#multiline">`<multiline>`</a> | `<multiline label="Item Description">`<br>`More text over`<br>`multiple lines.`<br>`</multiline>` | A tag used to represent dynamic content that can span multiple lines, and contain HTML. |
| <a href="http://www.campaignmonitor.com/create/editable-content/#image">`<img editable>`</a> | `<img editable label="Item Image" src="http://..." />` | A standard HTML image tag with an `editable` attribute added in to signify the `src` and `alt` attributes can be dynamically changed. |


### Mapping Objects to Layout Tags ###

In order to dynamically add content into an email template, the layouts and template tags within them need to be 'mapped' to an object in your system. Imagine your system has a 'Post' object with the following attributes:

 - title
 - body
 - image_url
 - url

In order to dynamically add data from a 'Post' into a layout, the layout needs to be told the class name of the object using the `data-object` attribute:

`<layout label="Top Post" data-object="Post"> ... </layout>`

These 'Post' attributes can be then dynamically injected into the layout as long as the template tags are told which attributes of the object to use. The easiest way to do this is to add HTML5 `data-` attributes to the tags in the template. There are three `data-` attributes you can add:

`data-content`
`data-href`
`data-alt`

They can be added to the `<singleline`, `<multiline`, and `<img editable>` tags as follows:

**singleline**

| Name | Example | Description |
| --- | --- | --- |
| `data-content` | `<singleline data-content="title">...</singleline>` | The post title will replace the `<singleline>` tags. |
| `data-href` | `<singleline data-href="url">...</singleline>` | The URL of the post will wrap the content. |

**multiline**

| Name | Example | Description |
| --- | --- | --- |
| `data-content` | `<multiline data-content="body">...</multiline>` | The post body will replace the `<multiline>` tags. |

**img**

| Name | Example | Description |
| --- | --- | --- |
| `data-content` | `<img editable data-content="image_url" />` | The post's image URL will be used for the `src` attribute. |
| `data-href` | `<img editable data-href="url" />` | The URL of the post will wrap the image. |
| `data-alt` | `<img editable data-alt="title" />` | The post's title will be used for the `alt` attribute. |

## Using the Subid Model ##

In order to dynamically add subid values onto links in email content, your system will need a way of saving subid values e.g. a 'Subid' model with `id` and `value` attributes. Specifying the model name, key attribute and value attribute in the config, you can select a subid value when creating an email. This can then by dynamically added onto links in the email when adding content from a mapped object.

If `subidModel`, `subidKey` and `subidVal` are specified in the config, a drop-down will be added to the /emails/email/create form so that a subid can be associated with an email.

### Dynamic Subid Links ###

Imagine your 'Post' model has a method named `getUrl()` that can be passed a `$subid_id` parameter. This method could be used to find a 'Subid' that can be dynamically appended to the URL of the post and then returned as a string. If you specify `data-content="getUrl"` as the mapped attribute for a template tag, the email system will attempt to call this method of the post object, passing a `$subid_id` value if the email has a 'Subid' associated with it.

Example `getUrl()` method:

```php

  /**
   * Get the post URL and dynamically add a
   * sub ID value on the end if possible
   *
   * @param $subid_id
   *
   * @return string
   */
  public function getUrl($subid_id=null)
  {
    $url = $this->url;

    if ($subid_id && $subid = Subid::model()->findByPk($subid_id)) {
      $url .= '?subid='.$subid->value;
    }

    return $url;
  }

```

### Example Template ###

```html

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <body>
      <table class="body">
        <tr>
          <td class="center" align="center" valign="top">
            <center>

            <repeater>

              <layout label="Top Post" data-object="Post">

                <table class="row">
                  <tr>
                    <td>

                      <h3 class="heading">
                        <singleline label="Title" data-content="title" data-href="getUrl">Post Title</singleline>
                      </h3>

                      <img editable="true" label="Image" class="center" data-content="image_url" data-href="getUrl" data-alt="title"/>

                      <multiline label="Body" data-content="body">Post body</multiline>

                      <table class="button radius success arrow">
                        <tr>
                          <td>
                            <singleline label="CTA" data-href="getUrl">Read more</singleline>
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>

              </layout>

              <layout label="More Posts 1" data-object="Post">
                <table class="row">
                  <tr>
                    <td>
                      <singleline label="Title" data-content="title" data-href="getUrl">Post Title</singleline>
                    </td>
                  </tr>
                </table>
              </layout>

              <layout label="More Posts 2" data-object="Post">
                <table class="row">
                  <tr>
                    <td>
                      <singleline label="Title" data-content="title" data-href="getUrl">Post Title</singleline>
                    </td>
                  </tr>
                </table>
              </layout>

              <layout label="More Posts 3" data-object="Post">
                <table class="row">
                  <tr>
                    <td>
                      <singleline label="Title" data-content="title" data-href="getUrl">Post Title</singleline>
                    </td>
                  </tr>
                </table>
              </layout>

            </repeater>

            </center>
          </td>
        </tr>
      </table>
    </body>
  </html>

```
