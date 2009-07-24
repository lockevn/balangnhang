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
        public virtual int SubtractVCoinOfVTCUser(string p_sUsername, string ItemCode, string p_sClientIP, int p_nMoneyToSubtract)
        {
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap12");
            string sGUIDTransactionCode = GURUCORE.Lib.Core.Text.TextHelper.NowToUTCString() + "#" + FunctionExtension.GetRandomGUID();

            object a = GApplication.GetInstance();
            
            string sVTCReturn = ws.BuyItem(p_sUsername, ItemCode, p_nMoneyToSubtract, p_sClientIP, VTC_KEYCODE, sGUIDTransactionCode);
            VTCResponseInfo response = VTCResponseInfo.Parse(sVTCReturn);

            int nRet = int.MinValue;
            if (response.ParseOK && int.Parse(response.GetItem(0, "-1")) > 0)
            {
                nRet = int.Parse(response.GetItem(1, int.MinValue.ToString()));
            }

            return nRet;
        }
        
    }
}
