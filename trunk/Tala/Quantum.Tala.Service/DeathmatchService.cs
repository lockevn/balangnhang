using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using Quantum.Tala.Service.VTCBillingService;

namespace Quantum.Tala.Service
{
    public class DeathmatchService : Quantum.Tala.Service.IDeathmatchService
    {
        [TransactionBound]
        public virtual List<string> SubtractVCoinBeforeStartSoi(List<TalaUser> arrUser, tournamentDTO tour)
        {
            MoneyService moneysvc = new MoneyService();

            List<string> arrRet = new List<string>();
            foreach (TalaUser user in arrUser)
            {
                if (tour.enrollfee > VTCIntecomService.GetBalanceOfVTCUser(user.Username))
                {
                    // thiếu tiền
                    arrRet.Add(user.Username);
                }                
            }

            // THREAT: có rủi ro ở đây, do không có transaction bound để gọi webAPI, có thể lúc kiểm thì đủ tiền, lúc trừ thật lại không đủ tiền

            // nếu không có chú nào thiếu tiền, mới bắt đầu tiến hành trừ
            if(arrRet.Count <= 0)
            {
                string sItemCode = tour.id + "#" + tour.name + "#" + tour.enrollfee;
                foreach (TalaUser user in arrUser)
                {
                    transactionDTO outputTransaction;
                    Quantum.Tala.Service.VTCBillingService.BuyItemsResponse outputResponse;
                    VTCIntecomService.SubtractVCoinOfVTCUser(
                        user.BankCredential.VTCAccountID, 
                        user.BankCredential.BankUsername,
                        sItemCode, user.IP, tour.enrollfee, out outputTransaction, out outputResponse);
                }
            }

            return arrRet;
        }
    }
}
