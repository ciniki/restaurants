//
// This is the main app for the restaurants module
//
function ciniki_restaurants_main() {
    //
    // The panel to list the menu
    //
    this.menus = new M.panel('menu', 'ciniki_restaurants_main', 'menus', 'mc', 'medium', 'sectioned', 'ciniki.restaurants.main.menus');
    this.menus.data = {};
    this.menus.sections = {
        'menus':{'label':'Menu', 'type':'simplegrid', 'num_cols':1,
            'noData':'Add your first menu',
            'addTxt':'Add Menu',
            'addFn':'M.ciniki_restaurants_main.edit.open(\'M.ciniki_restaurants_main.menus.open();\',0,null);'
            },
    }
    this.menus.cellValue = function(s, i, j, d) {
        if( s == 'menus' ) {
            switch(j) {
                case 0: return d.name;
            }
        }
    }
    this.menus.rowFn = function(s, i, d) {
        if( s == 'menus' ) {
            return 'M.ciniki_restaurants_main.menu.open(\'M.ciniki_restaurants_main.menus.open();\',\'' + d.id + '\');';
        }
    }
    this.menus.open = function(cb) {
        M.api.getJSONCb('ciniki.restaurants.menuList', {'tnid':M.curTenantID}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_restaurants_main.menus;
            p.data = rsp;
            p.refresh();
            p.show(cb);
        });
    }
    this.menus.addClose('Back');

    //
    // The panel to display Menu
    //
    this.menu = new M.panel('Menu', 'ciniki_restaurants_main', 'menu', 'mc', 'medium', 'sectioned', 'ciniki.restaurants.main.menu');
    this.menu.data = null;
    this.menu.menu_id = 0;
    this.menu.section_id = 0;
    this.menu.sections = {
        'details':{'label':'Menu', 'type':'simplegrid', 'num_cols':1, 'aside':'yes', 
            
            },
        'sections':{'label':'Sections', 'type':'simplegrid', 'num_cols':1, 'aside':'yes',
            'cellClasses':['multiline', ''],
            'noData':'Add a section to add items to your menu',
            'addTxt':'Add Section',
            'addFn':'M.ciniki_restaurants_main.section.open(\'M.ciniki_restaurants_main.menu.open();\',0,M.ciniki_restaurants_main.menu.menu_id);',
            'editFn':function(s, i, d) {
                return 'M.ciniki_restaurants_main.section.open(\'M.ciniki_restaurants_main.menu.open();\',\'' + d.id + '\');';
                },
            },
        'items':{'label':'', 'type':'simplegrid', 'num_cols':2,
            'visible':function() { return (M.ciniki_restaurants_main.menu.section_id > 0 ? 'yes':'no'); },
            'cellClasses':['multiline', ''],
            'noData':'No items for this section',
            'addTxt':'Add Item',
            'addFn':'M.ciniki_restaurants_main.item.open(\'M.ciniki_restaurants_main.menu.open();\',0,M.ciniki_restaurants_main.menu.section_id,M.ciniki_restaurants_main.menu.menu_id);',
            },
    }
    this.menu.cellValue = function(s, i, j, d) {
        if( s == 'details' ) {
            return d.value;
        }
        if( s == 'sections' ) {
            return '<span class="maintext">' + d.name + '</span><span class="subtext">' + d.intro + '</span>';
        }
        if( s == 'items' ) {
            switch(j) {
                case 0: return '<span class="maintext">' + d.name + '</span><span class="subtext">' + d.synopsis + '</span>';
                case 1: return d.price_display;
            }
        }
    }
    this.menu.rowClass = function(s, i, d) {
        if( s == 'sections' && d.id == this.section_id ) {
            return 'highlight';
        }
    }
    this.menu.rowFn = function(s, i, d) {
        if( s == 'sections' ) {
            return 'M.ciniki_restaurants_main.menu.sectionOpen(\'' + i + '\');';
        }
        if( s == 'items' ) {
            return 'M.ciniki_restaurants_main.item.open(\'M.ciniki_restaurants_main.menu.open();\',\'' + d.id + '\',M.ciniki_restaurants_main.menu.section_id,M.ciniki_restaurants_main.menu.menu_id);';
        }
    }
    this.menu.sectionOpen = function(sid) {
        var p = M.ciniki_restaurants_main.menu;
        if( p.section_id == 0 ) {
            p.size = 'medium narrowaside';
        }
        var section = p.data.sections[sid];
        p.sections.items.label = section.name;
        p.section_id = section.id;
        p.data.items = section.items;
        p.refresh();
        p.show();
    }
    this.menu.open = function(cb, mid) {
        if( mid != null ) { 
            if( this.menu_id != mid ) {
                this.section_id = 0;
            }
            this.menu_id = mid; 
        }
        M.api.getJSONCb('ciniki.restaurants.menuGet', {'tnid':M.curTenantID, 'menu_id':this.menu_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_restaurants_main.menu;
            p.data = rsp.menu;
            if( p.section_id > 0 ) {
                for(var i in p.data.sections) {
                    if( p.data.sections[i].id == p.section_id ) {
                        p.size = 'medium narrowaside';
                        p.data.items = p.data.sections[i].items;
                    }
                }
                if( p.size == 'medium' ) {
                    p.section_id = 0;
                }
            } else {
                p.size = 'medium';
            } 
            p.refresh();
            p.show(cb);
        });
    }
    this.menu.addButton('edit', 'Edit', 'M.ciniki_restaurants_main.edit.open(\'M.ciniki_restaurants_main.menu.open();\',M.ciniki_restaurants_main.menu.menu_id);');
    this.menu.addClose('Back');

    //
    // The panel to edit Menu
    //
    this.edit = new M.panel('Menu', 'ciniki_restaurants_main', 'edit', 'mc', 'medium', 'sectioned', 'ciniki.restaurants.main.edit');
    this.edit.data = null;
    this.edit.menu_id = 0;
    this.edit.sections = {
/*        '_primary_image_id':{'label':'Image', 'type':'imageform', 'aside':'yes', 'fields':{
            'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no',
                'addDropImage':function(iid) {
                    M.ciniki_restaurants_main.edit.setFieldValue('primary_image_id', iid);
                    return true;
                    },
                'addDropImageRefresh':'',
             },
        }}, */
        'general':{'label':'', 'fields':{
            'name':{'label':'Name', 'required':'yes', 'type':'text'},
            }},
        '_intro':{'label':'Introduction', 'fields':{
            'intro':{'label':'', 'hidelabel':'yes', 'type':'textarea'},
            }},
        '_notes':{'label':'Notes', 'fields':{
            'notes':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'medium'},
            }},
        '_buttons':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_restaurants_main.edit.save();'},
            'delete':{'label':'Delete', 
                'visible':function() {return M.ciniki_restaurants_main.edit.menu_id > 0 ? 'yes' : 'no'; },
                'fn':'M.ciniki_restaurants_main.edit.remove();'},
            }},
        };
    this.edit.fieldValue = function(s, i, d) { return this.data[i]; }
    this.edit.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.restaurants.menuHistory', 'args':{'tnid':M.curTenantID, 'menu_id':this.menu_id, 'field':i}};
    }
    this.edit.open = function(cb, mid) {
        if( mid != null ) { this.menu_id = mid; }
        M.api.getJSONCb('ciniki.restaurants.menuGet', {'tnid':M.curTenantID, 'menu_id':this.menu_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_restaurants_main.edit;
            p.data = rsp.menu;
            p.refresh();
            p.show(cb);
        });
    }
    this.edit.save = function(cb) {
        if( cb == null ) { cb = 'M.ciniki_restaurants_main.edit.close();'; }
        if( !this.checkForm() ) { return false; }
        if( this.menu_id > 0 ) {
            var c = this.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.restaurants.menuUpdate', {'tnid':M.curTenantID, 'menu_id':this.menu_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    eval(cb);
                });
            } else {
                eval(cb);
            }
        } else {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.restaurants.menuAdd', {'tnid':M.curTenantID}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_restaurants_main.edit.menu_id = rsp.id;
                eval(cb);
            });
        }
    }
    this.edit.remove = function() {
        if( confirm('Are you sure you want to remove menu?') ) {
            M.api.getJSONCb('ciniki.restaurants.menuDelete', {'tnid':M.curTenantID, 'menu_id':this.menu_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_restaurants_main.edit.close();
            });
        }
    }
    this.edit.addButton('save', 'Save', 'M.ciniki_restaurants_main.edit.save();');
    this.edit.addClose('Cancel');

    //
    // The panel to edit Section
    //
    this.section = new M.panel('Section', 'ciniki_restaurants_main', 'section', 'mc', 'medium', 'sectioned', 'ciniki.restaurants.main.section');
    this.section.data = null;
    this.section.menu_id = 0;
    this.section.section_id = 0;
    this.section.nplist = [];
    this.section.sections = {
        'general':{'label':'', 'fields':{
            'sequence':{'label':'Order', 'type':'text'},
            'name':{'label':'Name', 'required':'yes', 'type':'text'},
            }},
        '_intro':{'label':'Introduction', 'fields':{
            'intro':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'medium'},
            }},
        '_notes':{'label':'Notes', 'fields':{
            'notes':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'medium'},
            }},
        '_buttons':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_restaurants_main.section.save();'},
            'delete':{'label':'Delete', 
                'visible':function() {return M.ciniki_restaurants_main.section.section_id > 0 ? 'yes' : 'no'; },
                'fn':'M.ciniki_restaurants_main.section.remove();'},
            }},
        };
    this.section.fieldValue = function(s, i, d) { return this.data[i]; }
    this.section.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.restaurants.menuSectionHistory', 'args':{'tnid':M.curTenantID, 'section_id':this.section_id, 'field':i}};
    }
    this.section.open = function(cb, sid, mid, list) {
        if( sid != null ) { this.section_id = sid; }
        if( mid != null ) { this.menu_id = mid; }
        if( list != null ) { this.nplist = list; }
        M.api.getJSONCb('ciniki.restaurants.menuSectionGet', {'tnid':M.curTenantID, 'section_id':this.section_id, 'menu_id':this.menu_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_restaurants_main.section;
            p.data = rsp.section;
            p.refresh();
            p.show(cb);
        });
    }
    this.section.save = function(cb) {
        if( cb == null ) { cb = 'M.ciniki_restaurants_main.section.close();'; }
        if( !this.checkForm() ) { return false; }
        if( this.section_id > 0 ) {
            var c = this.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.restaurants.menuSectionUpdate', {'tnid':M.curTenantID, 'section_id':this.section_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    eval(cb);
                });
            } else {
                eval(cb);
            }
        } else {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.restaurants.menuSectionAdd', {'tnid':M.curTenantID, 'menu_id':this.menu_id}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_restaurants_main.section.section_id = rsp.id;
                eval(cb);
            });
        }
    }
    this.section.remove = function() {
        if( confirm('Are you sure you want to remove menusection?') ) {
            M.api.getJSONCb('ciniki.restaurants.menuSectionDelete', {'tnid':M.curTenantID, 'section_id':this.section_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_restaurants_main.section.close();
            });
        }
    }
    this.section.nextButtonFn = function() {
        if( this.nplist != null && this.nplist.indexOf('' + this.section_id) < (this.nplist.length - 1) ) {
            return 'M.ciniki_restaurants_main.section.save(\'M.ciniki_restaurants_main.section.open(null,' + this.nplist[this.nplist.indexOf('' + this.section_id) + 1] + ');\');';
        }
        return null;
    }
    this.section.prevButtonFn = function() {
        if( this.nplist != null && this.nplist.indexOf('' + this.section_id) > 0 ) {
            return 'M.ciniki_restaurants_main.section.save(\'M.ciniki_restaurants_main.section.open(null,' + this.nplist[this.nplist.indexOf('' + this.section_id) - 1] + ');\');';
        }
        return null;
    }
    this.section.addButton('save', 'Save', 'M.ciniki_restaurants_main.section.save();');
    this.section.addClose('Cancel');
    this.section.addButton('next', 'Next');
    this.section.addLeftButton('prev', 'Prev');

    //
    // The panel to edit Item
    //
    this.item = new M.panel('Item', 'ciniki_restaurants_main', 'item', 'mc', 'medium', 'sectioned', 'ciniki.restaurants.main.item');
    this.item.data = null;
    this.item.menu_id = 0;
    this.item.section_id = 0;
    this.item.item_id = 0;
    this.item.nplist = [];
    this.item.sections = {
/*        '_primary_image_id':{'label':'Image', 'type':'imageform', 'aside':'yes', 'fields':{
            'primary_image_id':{'label':'', 'type':'image_id', 'hidelabel':'yes', 'controls':'all', 'history':'no',
                'addDropImage':function(iid) {
                    M.ciniki_restaurants_main.item.setFieldValue('primary_image_id', iid);
                    return true;
                    },
                'addDropImageRefresh':'',
             },
        }}, */
        'general':{'label':'', 'fields':{
            'section_id':{'label':'Sections', 'type':'select', 'options':{}, 'complex_options':{'name':'name', 'value':'id'}},
            'sequence':{'label':'Order', 'type':'text'},
//            'code':{'label':'Code', 'type':'text'},
            'name':{'label':'Name', 'required':'yes', 'type':'text'},
            'price':{'label':'Price', 'type':'text'},
//            'foodtypes':{'label':'Food Types', 'type':'text'},
            }},
        '_synopsis':{'label':'Synopsis', 'fields':{
            'synopsis':{'label':'', 'hidelabel':'yes', 'type':'textarea', 'size':'small'},
            }},
        '_buttons':{'label':'', 'buttons':{
            'save':{'label':'Save', 'fn':'M.ciniki_restaurants_main.item.save();'},
            'delete':{'label':'Delete', 
                'visible':function() {return M.ciniki_restaurants_main.item.item_id > 0 ? 'yes' : 'no'; },
                'fn':'M.ciniki_restaurants_main.item.remove();'},
            }},
        };
    this.item.fieldValue = function(s, i, d) { return this.data[i]; }
    this.item.fieldHistoryArgs = function(s, i) {
        return {'method':'ciniki.restaurants.menuitemHistory', 'args':{'tnid':M.curTenantID, 'item_id':this.item_id, 'field':i}};
    }
    this.item.open = function(cb, iid, sid, mid, list) {
        if( iid != null ) { this.item_id = iid; }
        if( sid != null ) { this.section_id = sid; }
        if( mid != null ) { this.menu_id = mid; }
        if( list != null ) { this.nplist = list; }
        M.api.getJSONCb('ciniki.restaurants.menuItemGet', {'tnid':M.curTenantID, 'item_id':this.item_id, 'section_id':this.section_id}, function(rsp) {
            if( rsp.stat != 'ok' ) {
                M.api.err(rsp);
                return false;
            }
            var p = M.ciniki_restaurants_main.item;
            p.data = rsp.item;
            p.sections.general.fields.section_id.options = rsp.sections; 
            p.refresh();
            p.show(cb);
        });
    }
    this.item.save = function(cb) {
        if( cb == null ) { cb = 'M.ciniki_restaurants_main.item.close();'; }
        if( !this.checkForm() ) { return false; }
        if( this.item_id > 0 ) {
            var c = this.serializeForm('no');
            if( c != '' ) {
                M.api.postJSONCb('ciniki.restaurants.menuItemUpdate', {'tnid':M.curTenantID, 'item_id':this.item_id}, c, function(rsp) {
                    if( rsp.stat != 'ok' ) {
                        M.api.err(rsp);
                        return false;
                    }
                    eval(cb);
                });
            } else {
                eval(cb);
            }
        } else {
            var c = this.serializeForm('yes');
            M.api.postJSONCb('ciniki.restaurants.menuItemAdd', {'tnid':M.curTenantID, 'menu_id':this.menu_id}, c, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_restaurants_main.item.item_id = rsp.id;
                eval(cb);
            });
        }
    }
    this.item.remove = function() {
        if( confirm('Are you sure you want to remove menuitem?') ) {
            M.api.getJSONCb('ciniki.restaurants.menuItemDelete', {'tnid':M.curTenantID, 'item_id':this.item_id}, function(rsp) {
                if( rsp.stat != 'ok' ) {
                    M.api.err(rsp);
                    return false;
                }
                M.ciniki_restaurants_main.item.close();
            });
        }
    }
    this.item.nextButtonFn = function() {
        if( this.nplist != null && this.nplist.indexOf('' + this.item_id) < (this.nplist.length - 1) ) {
            return 'M.ciniki_restaurants_main.item.save(\'M.ciniki_restaurants_main.item.open(null,' + this.nplist[this.nplist.indexOf('' + this.item_id) + 1] + ');\');';
        }
        return null;
    }
    this.item.prevButtonFn = function() {
        if( this.nplist != null && this.nplist.indexOf('' + this.item_id) > 0 ) {
            return 'M.ciniki_restaurants_main.item.save(\'M.ciniki_restaurants_main.item.open(null,' + this.nplist[this.nplist.indexOf('' + this.item_id) - 1] + ');\');';
        }
        return null;
    }
    this.item.addButton('save', 'Save', 'M.ciniki_restaurants_main.item.save();');
    this.item.addClose('Cancel');
    this.item.addButton('next', 'Next');
    this.item.addLeftButton('prev', 'Prev');

    //
    // Start the app
    // cb - The callback to run when the user leaves the main panel in the app.
    // ap - The application prefix.
    // ag - The app arguments.
    //
    this.start = function(cb, ap, ag) {
        args = {};
        if( ag != null ) {
            args = eval(ag);
        }
        
        //
        // Create the app container
        //
        var ac = M.createContainer(ap, 'ciniki_restaurants_main', 'yes');
        if( ac == null ) {
            alert('App Error');
            return false;
        }
        
        this.menus.open(cb);
    }
}
