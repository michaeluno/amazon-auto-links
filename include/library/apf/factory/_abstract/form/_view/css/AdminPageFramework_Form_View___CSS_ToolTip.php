<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___CSS_ToolTip extends AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Base {
    protected function _get() {
        return <<<CSSRULES

/* Inside Field Title */        
th > label > span > .amazon-auto-links-form-tooltip {
    margin-top: 1px;
    float: right;
}
.postbox-container th > label > span > .amazon-auto-links-form-tooltip {
    margin-left: 1em;
    float: none;
}
        
/* Regular section titles have + button and collapsible title bar has a triangle icon so give a right margin */
.amazon-auto-links-section-title a.amazon-auto-links-form-tooltip,
.amazon-auto-links-collapsible-title a.amazon-auto-links-form-tooltip {
    margin-left: 1em;
}

/* When it is placed inside h2, h3, h4, the tooltip text becomes large so avoid that */
a.amazon-auto-links-form-tooltip > .amazon-auto-links-form-tooltip-content {
    font-size: 13px;
    font-weight: normal;
}

.amazon-auto-links-section-tab a.amazon-auto-links-form-tooltip {
    margin-left: 0.48em;
    color: #A8A8A8;
    vertical-align: middle;
}     
.amazon-auto-links-section-tab.nav-tab.active a.amazon-auto-links-form-tooltip {
    color: #A8A8A8;
}

/* Dashicon vertical alignment */
.amazon-auto-links-form-tooltip > span {
    margin-bottom: 1px;
    vertical-align: middle;
}

a.amazon-auto-links-form-tooltip {
    outline: none; 
    text-decoration: none;
    cursor: default;
    color: #A8A8A8;
}
a.amazon-auto-links-form-tooltip > .amazon-auto-links-form-tooltip-content > .amazon-auto-links-form-tooltip-title {
    font-weight: bold;
}
a.amazon-auto-links-form-tooltip strong {
    line-height:30px;
}
a.amazon-auto-links-form-tooltip:hover {
    text-decoration: none;
} 
a.amazon-auto-links-form-tooltip > span.amazon-auto-links-form-tooltip-content {

    display: none; 
    padding: 14px 20px 14px;
    margin-top: -30px; 
    margin-left: 28px;
    width: 400px; 
    line-height:16px;
    
    /* High z-index is required to appear over the left side bar menu */
    z-index: 100000;
    
}
a.amazon-auto-links-form-tooltip:hover > span.amazon-auto-links-form-tooltip-content{
    display: inline; 
    position: absolute; 
    color: #111;
    border:1px solid #DCA; 
    background: #FFFFF4;
    
    /* Adjust the position of the tooltip here */
    /* margin-left: -300px; */
}

/* Balloon Style */
/* .callout {
    z-index: 200000;
    position: absolute;
    top: 30px;
    border: 0;
    left: -12px;
}
 */

/* Tooltip Box Shadow */
a.amazon-auto-links-form-tooltip > span.amazon-auto-links-form-tooltip-content {
    border-radius:4px;
    box-shadow: 5px 5px 8px #CCC;
}

CSSRULES;
        
    }
}