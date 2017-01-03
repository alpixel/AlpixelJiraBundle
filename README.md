# AlpixelJiraBundle (WIP Do not use in production)
🍜   Query your Jira installation 

## Installation & Configuration

Active the bundle by add these line in your `AppKernel.php`
```
public function registerBundles()
{
    $bundles = [
      new Alpixel\Bundle\JiraBundle\AlpixelJiraBundle(),
    ];
}
```

Then in your `config.yml`

```
alpixel_jira:
    base_url: 'http://my.jira.fr/rest/api/2/'
    auth:
        method:
            basic:
                username: MyUsername
                password: MyPassword
```

Only the basic authentication is available for now.

## How to use

You need to use the service `alpixel_jira.api`

Example : 
```
  # MyAwesomeController.php
  ....
  public function apiAction() {
    $jira = $this->get('alpixel_jira.api';
    $response = $jira->get('/mypermission');
    $data = $response->getData();
    
    $response = $jira->search('(status=resolved AND project=SysAdmin) OR assignee=bobsmith');
    $data = $response->getData();
  }
```
