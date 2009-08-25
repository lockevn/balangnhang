using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using GURUCORE.Lib.Core.Security.Cryptography;
using GURUCORE.Framework.Core;
using GURUCORE.Framework.Business;
using GURUCORE.Framework.DataAccess;
using GURUCORE.Framework.DataAccess.ORMapping;

using Quantum.Tala.Lib;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using log4net;
using Quantum.Tala.Service.VTCGateTopup;

namespace Quantum.Tala.Service
{
    /// <summary>
    /// 
    /// </summary>
    public class VTCIntecomService
    {
        private static readonly ILog log = LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);

        // TODO: change to internal class

        const string SERVICECODE = "3006";
        const string MAKERCODE = "vtctelecom";

        const string FILEPATH_PRIVATEKEY = "Config/VTCPrivateKey.config";
        const string FILEPATH_PUBLICKEY = "Config/VTCPublicKey.config";

        static string Sign(string p_sOriginalData)
        {
            VTCDataSignature.DataSign ds = new VTCDataSignature.DataSign();
            string sDataSign = ds.GetSignatureXmlKey(p_sOriginalData, FILEPATH_PRIVATEKEY);
            return sDataSign;
        }





                        
        public static int AuthenticateVTC_MD5HashedPassword(string p_sUsername, string p_sHashedPassword)
        {
            // VTC authentication process here, to verify username password against VTC System
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap");

            string sSign = p_sUsername + "-" + p_sHashedPassword + "-" + VTCIntecomService.MAKERCODE;
            var response = ws.Authenticate(p_sUsername, p_sHashedPassword, VTCIntecomService.MAKERCODE, Sign(sSign));
            return response.AccountID;            
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="p_sUsername"></param>
        /// <returns>Trả về int.MinValue nếu lỗi</returns>        
        public static int GetBalanceOfVTCUser(string p_sVTCUsername)
        {
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap");
            string sSign = p_sVTCUsername + "-" + VTCIntecomService.MAKERCODE;
            var response = ws.GetBalance(p_sVTCUsername, VTCIntecomService.MAKERCODE, Sign(sSign));
            return response.Balance;            
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="p_sAccount"></param>
        /// <returns>int, AccountID, >0 if OK</returns>
        public static int CheckAccountEXISTS(string p_sAccount)
        {
            VTCGateTopup.VTCGateTopupSoapClient ws = new VTCGateTopup.VTCGateTopupSoapClient();
            string sSign = VTCIntecomService.SERVICECODE + "-" + p_sAccount + "-" + VTCIntecomService.MAKERCODE;
            string sVTCReturn = ws.CheckAccountEXISTS(VTCIntecomService.SERVICECODE, p_sAccount,
                VTCIntecomService.MAKERCODE, Sign(sSign));
            VTCResponseInfo response = VTCResponseInfo.Parse(sVTCReturn);

            int nAccountID = int.Parse(response.GetItem(0, "-1"));
            if (response.ParseOK && nAccountID > 0)
            {
                return nAccountID;
            }
            else
            {
                return int.MinValue;
            }
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="VTCAccountID"></param>
        /// <param name="p_sVTCUsername"></param>
        /// <param name="ItemCode"></param>
        /// <param name="p_sClientIP"></param>
        /// <param name="p_nMoneyToSubtract"></param>
        /// <param name="output">Các kết quả phụ sẽ trả ra ở đây. Hiện tại, ValueList đầu tiên là transid trong hệ thống log của Tala</param>
        /// <returns></returns>
        public static bool SubtractVCoinOfVTCUser(int VTCAccountID, string p_sVTCUsername, string ItemCode, string p_sClientIP, int p_nMoneyToSubtract, out transactionDTO outputTransactionDTO)
        {
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap");
            string sGUIDTransactionCode = GURUCORE.Lib.Core.Text.TextHelper.NowToUTCString() + "#" + FunctionExtension.GetRandomGUID();

            string sSign = p_sVTCUsername + "-" + VTCAccountID.ToString() + "-" + VTCIntecomService.MAKERCODE + "-" +
                ItemCode + "-" + p_nMoneyToSubtract.ToString() + "-" + sGUIDTransactionCode;


            IMoneyService moneysvc = ServiceLocator.Locate<IMoneyService, MoneyService>();
            #region log các hành vi giao dịch tiền, tham gia, ...");
            transactionDTO tranEntry = new transactionDTO
            {
                amount = p_nMoneyToSubtract,                
                meta = p_sVTCUsername,
                meta1 = ItemCode,
                meta2 = p_sClientIP,
                type = (int)MoneyTransactionType.Subtract,
                status = transactionDTO.STATUS_PROCESSING
            };

            try
            {
                tranEntry = moneysvc.CreateTransation(tranEntry);
            }
            catch (System.Exception ex)
            {
                log.Error(
                    string.Format("Can't write transaction log to DB. I write here. No call to VTC was made.^{0},{1},{2},{3},{4},{5},{6}",
                    tranEntry.amount,
                    tranEntry.desc,
                    tranEntry.meta,
                    tranEntry.meta1,
                    tranEntry.meta2,                    
                    MoneyTransactionType.Subtract.ToString(),
                    tranEntry.status)
                    ,ex);

                outputTransactionDTO = tranEntry;
                return false;
            }

            #endregion

            // không log được thì sẽ không gọi hàm tới VTC
            var response = ws.BuyItem(p_sVTCUsername, VTCAccountID, VTCIntecomService.MAKERCODE, ItemCode, p_nMoneyToSubtract, p_sClientIP, sGUIDTransactionCode, Sign(sSign));            
            if (response.ResponseCode == "0")
            {
                tranEntry.status = transactionDTO.STATUS_OK;
                moneysvc.SaveTransation(tranEntry);
                outputTransactionDTO = tranEntry;
                return true;
            }
            else
            {
                tranEntry.status = transactionDTO.STATUS_FAILED;
                tranEntry.desc = string.Format("ResponseCode:{0},ResponseDesc:{1}",response.ResponseCode, response.Decription);
                moneysvc.SaveTransation(tranEntry);
                outputTransactionDTO = null;
                return false;
            }
        }




        public static double AddVCoinOfVTCUser(string p_sAccount, string ItemCode, string p_sClientIP, int p_nMoneyToAdd, out VTCGateResponse output)
        {
            if (CheckAccountEXISTS(p_sAccount) <= 0)
            {
                // lỗi user không tồn tại
                output = null;
                return -1;
            }

            VTCGateTopup.VTCGateTopupSoapClient ws = new VTCGateTopup.VTCGateTopupSoapClient();
            // string sGUIDTransactionCode = GURUCORE.Lib.Core.Text.TextHelper.NowToUTCString() + "#" + FunctionExtension.GetRandomGUID();

            /// - Orgtransid (long, required): Mã giao dịch phát sinh từ phía Partner và không trùng nhau. Có nghĩa là mỗi giao dịch chỉ gửi 1 lần. 
            Random rand = new Random(DateTime.Now.Millisecond);
            long Orgtransid = (long)rand.Next();
            DateTime Transdate = DateTime.Now;
            
            string sSign = VTCIntecomService.SERVICECODE + "-"
                + p_sAccount + "-"
                + Convert.ToInt32(p_nMoneyToAdd).ToString() + "-" +
                VTCIntecomService.MAKERCODE + "-" +
                Transdate.ToString("MMddyyyy") + "-" + Orgtransid;

            var response = ws.TopupAccount(VTCIntecomService.SERVICECODE, 
                p_sAccount, p_nMoneyToAdd, 
                VTCIntecomService.MAKERCODE, Transdate, Orgtransid, Sign(sSign));

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
                

                try
                {
                    tranEntry = DAU.AddObject<transactionDTO>(tranEntry);
                }
                catch (System.Exception ex)
                {
                    log.Error(
                        string.Format("Can't write transaction log to DB. I write here.^{0},{1},{2},{3},{4},{5}",
                        p_nMoneyToAdd,
                        p_sClientIP,
                        p_sAccount,
                        ItemCode,
                        p_sClientIP,
                        MoneyTransactionType.Add.ToString(),
                        ex)
                    );
                }
                
                #endregion

                output = response;
                return response.Amount;
            }
            else
            {
                // lỗi nghiêm trọng, server VTC có thể bị giả mạo
                output = response;
                return int.MinValue;
            }

        }
    }
}
