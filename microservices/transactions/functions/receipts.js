const escpos = require('escpos');

const self = {
    device: () => {
        try{
            //var device = new escpos.USB(0x04b8, 0x0e15);
            var device  = new escpos.Network('http://localhost:631/printers/EPSON-TM-T20II-2',631);
            return device;
        }catch(e){
            console.log(e);
            return false;
        }
    },
    // const device  = new escpos.Network('localhost'); // TODO: Production, add to CFG
    printer: false,
	openCashdesk: () => {
        if(!self.printer){
            self.printer = new escpos.Printer(self.device());
            self.printer.cashdraw();
            return true;
        }else{
            return false;
        }
	},
    print: (receipt) => {
        //self.encodeTest(); return;
        return new Promise ( (resolve, reject) => {
            if (!self.device()) reject("No printer device"); // missing device, reject
            else{
                const space = "    ",
                options = { encoding: "CP852", width:64 };

                var device = self.device();
                self.printer = new escpos.Printer(device, options);

                // open cashdesk
                if(receipt.openCashdesk == 1) self.printer.cashdraw(); 

                escpos.Image.load(__dirname + '/logo_tm_printer.png', function(image){

                    device.open(function(err){
                        if (err) reject(err); // Port busy / printer non-existent
                        else {
                            // Logo
                            self.printer.align('ct');
                            self.printer.raster(image);

                            // set codepage - tm printer encoding
                            self.printer.setCharacterCodeTable(18); 

                            // set Font
                            self.printer.font('b').style('').size();

                            // Subject info
                            self.printer
                            .feed()
                            .text(receipt.subject_info.name)
                            .text(receipt.subject_info.street)
                            .text(`${receipt.subject_info.zip} ${receipt.subject_info.town}`)
                            .text(`IČO:${receipt.subject_info.company_id}`)
                            .feed(2)

                            .align('lt')

                            // User and date
                            .tableCustom([ 
                                { width:0.5, align:"LEFT", text:`Tisk provedl: ${receipt.created_by}` },
                                { width:0.5, align:"RIGHT", text:receipt.date }
                            ]);

                            // Items
                            let totalValue = 0, totalDiscount = 0, dph21 = 0, dph15 = 0, dph21Base = 0, dph15Base = 0;

                            self.printer
                            .drawLine()
                            .style('b')
                            .tableCustom([ 
                                { width:0.55, align:"LEFT",  text:'Položka:' },
                                { width:0.08, align:"RIGHT", text:'Množ' },
                                { width:0.08, align:"RIGHT", text:'DPH' },
                                { width:0.12, align:"RIGHT", text:'Kč/jed' },
                                { width:0.12, align:"RIGHT", text:'Cena Kč' }
                            ]);
                            self.printer.style('');
                            receipt.items.forEach(item => {
                                self.printer.tableCustom([ 
                                    { width:0.55, align:"LEFT",  text:item.title },
                                    { width:0.08, align:"RIGHT", text:item.amount },
                                    { width:0.08, align:"RIGHT", text:item.dph },
                                    { width:0.12, align:"RIGHT", text:item.price },
                                    { width:0.12, align:"RIGHT", text:item.price * item.amount }
                                ]);

                                // Multisport item
                                if(item.id == receipt.multisportItem){
                                    self.printer.tableCustom([
                                        { width:0.70, align:"LEFT",  text:`${space}hrazeno multisport kartou` },
                                        { width:0.29, align:"RIGHT", text:`-${item.price}` }
                                    ]);
                                    item.amount--;    
                                }

                                // Discount
                                if(item.discount>0){
                                    let discountValue = parseFloat((item.price/100 * item.discount).toFixed(2)); 
                                    self.printer.tableCustom([
                                        { width:0.70, align:"LEFT",  text:`${space}sleva ${item.discount}%` },
                                        { width:0.29, align:"RIGHT", text:`-${discountValue}` }
                                    ]);
                                    totalDiscount += discountValue;
                                    item.price -= discountValue;
                                }
                                
                                totalValue += (item.price * item.amount);
                                // VAT
                                if(item.dph==0.21){
                                    let taxBase = (item.price / (1+item.dph));
                                    dph21Base += taxBase * item.amount;
                                    dph21 += ( item.price - taxBase ) * item.amount;
                                } else if (item.dph==0.15){
                                    let taxBase = (item.price / (1+item.dph));
                                    dph15Base += taxBase * item.amount;
                                    dph15 += ( item.price - taxBase ) * item.amount;
                                }
                            });
                            self.printer
                            .drawLine()
                            .size(2,1)
                            .tableCustom([
                                { width:0.23, align:"LEFT",  text:'Celkem' },
                                { width:0.14, align:"RIGHT", text:`${totalValue} Kč` }
                            ]);
                            self.printer.feed();

                            self.printer.font('b').style('').size();

                            // Total discount
                            if(totalDiscount > 0){
                                self.printer.tableCustom([
                                    { width:0.70, align:"LEFT",  text:`Sleva byla` },
                                    { width:0.29, align:"RIGHT", text:`${totalDiscount} Kč` }
                                ]);
                            }

                            // print purchase types only if client must pay some money
                            if((totalValue) > 0){ 
                                self.printer.style('b').text('Platba:').style('');
                                receipt.purchaseTypes.forEach(pt => {
                                    if(pt.id==4) return; 
                                    self.printer.tableCustom([
                                        { width:0.5, align:"LEFT",  text:`${space}${pt.title}` },
                                        { width:0.5, align:"RIGHT", text:`${pt.price} Kč` }
                                    ]);

                                });
                            }

                            self.printer.feed();

                            // VAT 21 %
                            if(dph21 > 0){
                                self.printer.tableCustom([
                                    { width:0.5, align:"LEFT",  text:`DPH 21.00%` },
                                    { width:0.5, align:"RIGHT", text:`${dph21.toFixed(2)} Kč` }
                                ]);
                                self.printer.tableCustom([
                                    { width:0.5, align:"LEFT",  text:`Základ DPH 21.00%` },
                                    { width:0.5, align:"RIGHT", text:`${dph21Base.toFixed(2)} Kč` }
                                ]);
                            }
                            
                            // VAT 15 %
                            if(dph15 > 0){
                                self.printer.tableCustom([
                                    { width:0.5, align:"LEFT",  text:`DPH 15.00%` },
                                    { width:0.5, align:"RIGHT", text:`${dph15.toFixed(2)} Kč` }
                                ]);
                                self.printer.tableCustom([
                                    { width:0.5, align:"LEFT",  text:`Základ DPH 15.00%` },
                                    { width:0.5, align:"RIGHT", text:`${dph15Base.toFixed(2)} Kč` }
                                ]);                
                            }
                            
                            // Trans info
                            self.printer.drawLine().align('ct');
                            //printer.text(`DIČ:$subject_info->vat_id`);
                            self.printer.text(`ID provozovny:${receipt.gymCode}`);
                            self.printer.text(`ID pokladny:${receipt.checkoutId}`);
                            self.printer.text(`ID účtenky:${receipt.receiptId}`);
                            //printer.text(receipt.eet_id);
                            //printer.text(receipt.transactionId);
                            self.printer.style('b').text(`Tržba v běžném režimu`).style('');       
                    
                            // Footer
                            self.printer.feed(2).size(2,1).text(`www.gymit.cz`).feed()
                            .cut()
                            .close(() => {
                                self.printer = false;
                                resolve();
                            });
                        }
                    });
                });
            }

        });
    },
    encodeTest: () => {
        var device = self.device();
        device.open(function(err){
            self.printer = new escpos.Printer(device);
            self.printer.width = 64; // because of font B -> font A = length 48;
            self.printer.align('ct').font('b').style('').size();
            self.printer.setCharacterCodeTable(18)
            .encode('cp852').text('cp852 - ěščřžýáíé')
            .encode('cp28592').text('cp28592 - ěščřžýáíé')
            .encode('iso88591').text('iso88591 - ěščřžýáíé').encode('iso88593').text('iso88593 - ěščřžýáíé').encode('iso885915').text('iso885915 - ěščřžýáíé')
            .encode('GB18030').text('GB18030 - ěščřžýáíé').encode('EUC-KR').text('EUC_KR - ěščřžýáíé')
            .encode('CP437').text('CP437 - ěščřžýáíé').encode('CP737').text('CP737 - ěščřžýáíé').encode('CP775').text('CP775 - ěščřžýáíé').encode('CP850').text('CP850 - ěščřžýáíé').encode('CP852').text('CP852 - ěščřžýáíé').encode('CP855').text('CP855 - ěščřžýáíé').encode('CP857').text('CP857 - ěščřžýáíé').encode('CP858').text('CP858 - ěščřžýáíé').encode('CP860').text('CP860 - ěščřžýáíé').encode('CP861').text('CP861 - ěščřžýáíé').encode('CP863').text('CP863 - ěščřžýáíé').encode('CP865').text('CP865 - ěščřžýáíé').encode('CP866').text('CP866 - ěščřžýáíé').encode('CP869').text('CP869 - ěščřžýáíé')
            .encode('IBM437').text('IBM437 - ěščřžýáíé').encode('IBM737').text('IBM737 - ěščřžýáíé').encode('IBM775').text('IBM775 - ěščřžýáíé').encode('IBM850').text('IBM850 - ěščřžýáíé').encode('IBM852').text('IBM852 - ěščřžýáíé').encode('IBM855').text('IBM855 - ěščřžýáíé').encode('IBM857').text('IBM857 - ěščřžýáíé').encode('IBM858').text('IBM858 - ěščřžýáíé').encode('IBM860').text('IBM860 - ěščřžýáíé').encode('IBM861').text('IBM861 - ěščřžýáíé').encode('IBM863').text('IBM863 - ěščřžýáíé').encode('IBM865').text('IBM865 - ěščřžýáíé').encode('IBM866').text('IBM866 - ěščřžýáíé').encode('IBM869').text('IBM869 - ěščřžýáíé')
            .encode('CP1250').text('CP1250 - ěščřžýáíé').encode('CP1251').text('CP1251 - ěščřžýáíé').encode('CP1252').text('CP1252 - ěščřžýáíé').encode('CP1253').text('CP1253 - ěščřžýáíé').encode('CP1254').text('CP1254 - ěščřžýáíé').encode('CP1255').text('CP1255 - ěščřžýáíé').encode('CP1256').text('CP1256 - ěščřžýáíé').encode('CP1257').text('CP1257 - ěščřžýáíé').encode('CP1258').text('CP1258 - ěščřžýáíé')
            .encode('WIN1250').text('WIN1250 - ěščřžýáíé').encode('WIN1251').text('WIN1251 - ěščřžýáíé').encode('WIN1252').text('WIN1252 - ěščřžýáíé').encode('WIN1253').text('WIN1253 - ěščřžýáíé').encode('WIN1254').text('WIN1254 - ěščřžýáíé').encode('WIN1255').text('WIN1255 - ěščřžýáíé').encode('WIN1256').text('WIN1256 - ěščřžýáíé').encode('WIN1257').text('WIN1257 - ěščřžýáíé').encode('WIN1258').text('WIN1258 - ěščřžýáíé')
            .encode('UTF-8').text('UTF-8 - ěščřžýáíé').encode('UTF-16BE').text('UTF-16BE - ěščřžýáíé').encode('UTF-16').text('UTF-16 - ěščřžýáíé')            
            .cut()
            .close();
        });
    }

};

module.exports = self;