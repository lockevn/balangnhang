using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Quantum.Tala.Service.Business;

using GURUCORE.Framework.Core.Data;
using GURUCORE.Framework.DataAccess;
using GURUCORE.Framework.DataAccess.ORMapping;
using GURUCORE.Framework.DataAccess.ORMapping.CriteriaMapping;
using GURUCORE.Framework.Business;

//using GURUCORE.Portal.Module.Ad.Service.Business.DTO;
using GURUCORE.Framework.Business.DTO;
using Quantum.Tala.Service.DTO;


namespace Quantum.Tala.Service
{
    public class TournamentService : BusinessService, Quantum.Tala.Service.ITournamentService
    {
        [TransactionBound]
        public virtual int CreateTournament(tournamentDTO p_dto)
        {
            return DAU.AddObject<tournamentDTO>(p_dto).id;
        }
                
        [TransactionBound]
        public virtual tournamentDTO[] GetTournamentList()
        {
            DTOCollection<tournamentDTO> arr = DAU.GetObjects<tournamentDTO>(
                new Expression(tournamentDTO.ISENABLED_FLD, Operator.Eq, 1) *
                new Expression(tournamentDTO.ENDTIME_FLD, Operator.Gte, DateTime.Now)
                );
            return arr.ToArray();
        }

        public virtual tournamentDTO[] GetTournamentOfUser(string username)
        {            
            DTOCollection<tournamentDTO> arr = DAU.GetObjects<tournamentDTO>(
                new ExpressionSQL("game_tournament.id in (select tournamentid from game_user_tournament where u = '" + username + "')")
                );
            return arr.ToArray();
        }





        [TransactionBound]
        public virtual int CreateSoi(soiDTO p_dto)
        {
            return DAU.AddObject<soiDTO>(p_dto).id;
        }



        [TransactionBound]
        public virtual int AddUserToTournament(string sTalaUsername, int nBankAccountID, string sBankUsername, string ip, tournamentDTO tour)
        {            
            /// Trừ tiền user"+tour.enrollfee+@", gọi sang nghiệp vụ VTC VCoin
            MoneyService moneysvc = new MoneyService();            
            string sItemCode = tour.id + "#" + tour.name + "#" + tour.enrollfee;

            transactionDTO outputTransaction;
            Quantum.Tala.Service.VTCBillingService.BuyItemsResponse outputResponse;
            bool bSubtractOK = VTCIntecomService.SubtractVCoinOfVTCUser(nBankAccountID, sBankUsername, sItemCode, ip, tour.enrollfee, out outputTransaction, out outputResponse);

            int nTransactionDTO_ID = -1;

            if (bSubtractOK)
            {
                if (null != outputTransaction)
                {
                    nTransactionDTO_ID = outputTransaction.id;
                }

                /// Ghi vào bản danh sách đăng ký tham gia giải, Cấp point khởi động cho user
                user_tournamentDTO ticket = new user_tournamentDTO
                {
                    desc = "đóng tiền gia nhập tournament",
                    tournamentid = tour.id,
                    transactionid = nTransactionDTO_ID,
                    u = sTalaUsername,
                    point = tour.startuppoint
                };
                DAU.AddObject<user_tournamentDTO>(ticket);
                // IMPROVE: cập nhật lại danh sách TalaUser.AttendingTournament, hiện tại đang cập nhật ở ngoài hàm API web
                return ticket.transactionid;
            }
            else
            {
                return nTransactionDTO_ID;
            }            
        }

	}
}
