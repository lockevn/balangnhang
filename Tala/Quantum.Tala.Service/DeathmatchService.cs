using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;

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
                if (tour.enrollfee > moneysvc.GetBalanceOfVTCUser(user.Username))
                {
                    // thiếu tiền
                    arrRet.Add(user.Username);
                }                
            }

            // nếu không có chú nào thiếu tiền, mới bắt đầu tiến hành trừ
            if(arrRet.Count <= 0)
            {
                string sItemCode = tour.id + "#" + tour.name + "#" + tour.enrollfee;
                foreach (TalaUser user in arrUser)
                {
                    moneysvc.SubtractVCoinOfVTCUser(user.BankCredential.BankUsername, sItemCode, user.IP, tour.enrollfee);
                }
            }

            return arrRet;
        }
    }
}
