# Installation

Install the pakage using composer

composer require theiconnz/campaigns

bin/magento setup:upgrade

bin/magento setup:di:compile

---

> Go to Admin -> Marketing -> Campaigns

Add new campaigns
View Results


---

## Create url rewrite in front end

For campaign page url is **campaigns/campaign/view/id/x**

You can create url rewrite for above url in Url Rewrite section

For results page url is **campaigns/campaign/results/id/x**

> for results page **x is campaign id**




## frontend classes to use in results tiles

Main containers

.content-tile-1

.content-tile-2

.content-tile-3

Inside main containers we can use sub clases to arange sub tiles

.data-content

.image-tile-1

.image-tile-2

.image-sub-tile-1

.image-sub-tile-2


> .image-tile-x and .image-sub-tile-x must insert in to image media type of the Magento editor
