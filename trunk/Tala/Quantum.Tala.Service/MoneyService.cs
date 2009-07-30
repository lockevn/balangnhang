using System;
using GURUCORE.Framework.Business;
using GURUCORE.Framework.DataAccess;
using GURUCORE.Framework.DataAccess.ORMapping;
using Quantum.Tala.Lib;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Lib.Core.Security.Cryptography;
using System.Data.Common;
using GURUCORE.Framework.Core;


namespace Quantum.Tala.Service
{
    public class MoneyService : BusinessService, Quantum.Tala.Service.IMoneyService
    {
        const string VTC_KEYCODE = "5357DE0E4B2D7FCEDBE9EF849BFDB7AF";
        const string VTC_SERVICECODE = "3006";        
        const string VTC_MAKERCODE = "VTC";
        
        /// <summary>
        /// 
        /// </summary>
        /// <param name="p_sUsername"></param>
        /// <returns>Trả về int.MinValue nếu lỗi</returns>
        [TransactionBound]
        public virtual int GetBalanceOfVTCUser(string p_sUsername)
        {            
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap12");
            string sVTCReturn = ws.GetBalanceVcoin(p_sUsername);
            VTCResponseInfo response = VTCResponseInfo.Parse(sVTCReturn);

            int nRet = int.MinValue;
            if (response.ParseOK && int.Parse(response.GetItem(0, "-1")) > 0)
            {
                nRet = int.Parse(response.GetItem(1, int.MinValue.ToString()));                                
            }

            return nRet;
        }


        [TransactionBound]
        public virtual int SubtractVCoinOfVTCUser(string p_sUsername, string ItemCode, string p_sClientIP, int p_nMoneyToSubtract, OutputResultObject output)
        {
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap12");
            string sGUIDTransactionCode = GURUCORE.Lib.Core.Text.TextHelper.NowToUTCString() + "#" + FunctionExtension.GetRandomGUID();
            
            string sVTCReturn = ws.BuyItem(p_sUsername, ItemCode, p_nMoneyToSubtract, p_sClientIP, VTC_KEYCODE, sGUIDTransactionCode);
            VTCResponseInfo response = VTCResponseInfo.Parse(sVTCReturn);

            int nRet = int.MinValue;
            if (response.ParseOK && int.Parse(response.GetItem(0, "-1")) > 0)
            {
                nRet = int.Parse(response.GetItem(1, int.MinValue.ToString()));

                #region log các hành vi giao dịch tiền, tham gia, ...");
                transactionDTO tranEntry = new transactionDTO
                {
                    amount = p_nMoneyToSubtract,
                    desc = p_sClientIP,
                    meta = p_sUsername,
                    meta1 = ItemCode,
                    meta2 = p_sClientIP,
                    type = (int)MoneyTransactionType.Subtract
                };
                tranEntry = DAU.AddObject<transactionDTO>(tranEntry);
                #endregion

                if (null != output)
                {
                    output.ValueList.Add(tranEntry.id);
                }
            }

            return nRet;
        }

        [TransactionBound]
        public virtual bool CheckAccountEXISTS(string p_sAccount)
        {                        
            VTCGateTopup.VTCGateTopupSoapClient ws = new VTCGateTopup.VTCGateTopupSoapClient();            
            VTCDataSignature.DataSign ds = new VTCDataSignature.DataSign();
            string orgrinData = VTC_SERVICECODE + "-" + p_sAccount + "-" + VTC_MAKERCODE;
            string sDataSign = ds.GetSignatureXmlKey(orgrinData, "PrivateKeyTest.xml");
            string sVTCReturn = ws.CheckAccountEXISTS(VTC_SERVICECODE, p_sAccount, VTC_MAKERCODE, sDataSign);
            VTCResponseInfo response = VTCResponseInfo.Parse(sVTCReturn);

            if (response.ParseOK && int.Parse(response.GetItem(0, "-1")) > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }


        [TransactionBound]
        public virtual int AddVCoinOfVTCUser(string p_sAccount, string ItemCode, string p_sClientIP, int p_nMoneyToAdd)
        {
            if (CheckAccountEXISTS(p_sAccount) == false)
            {
                // lỗi user không tồn tại
                return -1;
            }

            VTCGateTopup.VTCGateTopupSoapClient ws = new VTCGateTopup.VTCGateTopupSoapClient();
            // string sGUIDTransactionCode = GURUCORE.Lib.Core.Text.TextHelper.NowToUTCString() + "#" + FunctionExtension.GetRandomGUID();
            
            /// - Orgtransid (long, required): Mã giao dịch phát sinh từ phía Partner và không trùng nhau. Có nghĩa là mỗi giao dịch chỉ gửi 1 lần. 
            Random rand = new Random(DateTime.Now.Millisecond);
            long Orgtransid = (long)rand.Next();
            DateTime Transdate = DateTime.Now;

            /*
             * - DataSign 
Các thông tin ký gồm: ServiceCode + "-" + Account +"-" + 
Convert.ToInt32(Amount).ToString()+ "-" + MakerCode + "-"  + 
Transdate(MMddyyyy) + "-" + Orgtransid 
             */
            VTCDataSignature.DataSign ds = new VTCDataSignature.DataSign();
            string orgrinData = VTC_SERVICECODE + "-" + p_sAccount + "-" + Convert.ToInt32(p_nMoneyToAdd).ToString() + "-" + 
                VTC_MAKERCODE + "-" + Transdate.ToString("MMddyyyy") + "-" + Orgtransid;
            
            string sDataSign = ds.GetSignatureXmlKey(orgrinData, "PrivateKeyTest.xml");
            var VTCReturn = ws.TopupAccount(VTC_SERVICECODE, p_sAccount, p_nMoneyToAdd, VTC_MAKERCODE, Transdate, Orgtransid, sDataSign);

            /*
Output:  
Bao gồm trường thông tin mà  đối tác gửi  đến và thêm VTCTransCode, 
ResponseCode, Description thể hiện dưới dạng XML 

- VTCTransCode: Mã giao dịch phát sinh từ phía VTC Paygate. 
- ResponseCode: Thể hiện kết quả giao dịch  1. (Có phụ lục mã lỗi 
kèm theo mỗi dịch vụ) 
- Description: Mã giao dịch phát sinh từ phía Partner và không trùng 
nhau. Có nghĩa là mỗi giao dịch chỉ gửi 1 lần. 

- DataSign: Chữ ký  điện tử trên giao dịch tương  ứng của VTC’s 
Partner. Chữ ký điện tử theo thuật toán RSA 1024 bit bằng private key 
của VTC tạo trong cặp key và cung cấp cho Partner public key để xác 
nhận bản tin mà VTC trả về.   
Các thông tin kiểm tra chữ ký:  
ServiceCode + "-" + Account + "-" + Convert.ToInt32(Amount).ToString() + 
"-" + MakerCode + "-" + Transdate(MMddyyyy) + "-" + Orgtransid + "-"  + 
ResponseCode 
             */            

            // check lại chữ ký của VTC Response xem ok chưa
            if (true)
            {
                #region log các hành vi giao dịch tiền, tham gia, ...");
                transactionDTO tranEntry = new transactionDTO
                {
                    amount = p_nMoneyToAdd,
                    desc = p_sClientIP,
                    meta = p_sAccount,
                    meta1 = ItemCode,
                    meta2 = p_sClientIP,
                    type = (int)MoneyTransactionType.Add
                };
                tranEntry = DAU.AddObject<transactionDTO>(tranEntry);
                #endregion
                return p_nMoneyToAdd;
            }
            else
            {
                // lỗi nghiêm trọng, server VTC có thể bị giả mạo
                return int.MinValue;
            }
            
        }
    }
}
