CREATE TABLE `inv_invoices` (
  `tenant` int(11) NOT NULL DEFAULT '1',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `external_id` text NOT NULL,
  `crm_customers_id` int(11) NOT NULL,
  `crm_addrs_bill_id` int(11) NOT NULL,
  `payment_state` int(11) NOT NULL DEFAULT '0',
  `bill_state` int(11) NOT NULL DEFAULT '0',
  `total_limit` int(11) NOT NULL,
  `payment_discount_percentage` int(11) NOT NULL,
  `date` date NOT NULL,
  `creator` int(11) NOT NULL,
  `last_editor` int(11) NOT NULL,
  `create_time` bigint(20) NOT NULL,
  `modify_time` bigint(20) NOT NULL,
  PRIMARY KEY (`tenant`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8