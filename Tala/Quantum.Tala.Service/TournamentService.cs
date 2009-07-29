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
        public virtual int AddUserToTournament(string sTalaUsername, string sBankUsername, string ip, tournamentDTO tour)
        {            
            /// Trừ tiền user"+tour.enrollfee+@", gọi sang nghiệp vụ VTC VCoin
            MoneyService moneysvc = new MoneyService();            
            string sItemCode = tour.id + "#" + tour.name + "#" + tour.enrollfee;
            moneysvc.SubtractVCoinOfVTCUser(sBankUsername, sItemCode, ip, tour.enrollfee);
            
            /// log các hành vi giao dịch tiền, tham gia, ...");
            transactionDTO tranEntry = new transactionDTO
            {
                amount = tour.enrollfee,
                desc = ip,
                meta = sTalaUsername,
                meta1 = sItemCode,
                meta2 = ip,
                type = 1    /* trừ tiền user */                
            };
            tranEntry = DAU.AddObject<transactionDTO>(tranEntry);

            /// Ghi vào bản danh sách đăng ký tham gia giải, Cấp point khởi động cho user
            user_tournamentDTO ticket = new user_tournamentDTO
            {
                desc = "đóng tiền gia nhập tournament",
                tournamentid = tour.id,
                transactionid = tranEntry.id,
                u = sTalaUsername,
                point = tour.startuppoint
            };            
            DAU.AddObject<user_tournamentDTO>(ticket);

            return tranEntry.id;
        }
        


        //[TransactionBound]
        //public virtual VwBannerLocalizedWithBlockDTO[] GetBannerListForView(int p_nBlockID, int p_nPage, int p_nPageCount)
        //{
        //    Order ordBannerID = new Order(VwBannerLocalizedWithBlockDTO.DISPLAYORDER_FLD, Order.ASC);

        //    Criteria crtCondition;
        //    if (p_nBlockID > 0)
        //    {
        //        crtCondition = new Criteria(
        //            new Expression(VwBannerLocalizedWithBlockDTO.BLOCKID_FLD, Operator.Eq, p_nBlockID),
        //            (p_nPage - 1) * p_nPageCount,
        //            p_nPageCount,
        //            ordBannerID);
        //    }
        //    else
        //    {
        //        crtCondition = new Criteria(
        //            null,
        //            (p_nPage - 1) * p_nPageCount,
        //            p_nPageCount,
        //            ordBannerID);
        //    }

        //    DTOCollection<VwBannerLocalizedWithBlockDTO> arrBanner = DAU.GetMultiLocalizedObject<VwBannerLocalizedWithBlockDTO>(crtCondition);

        //    return arrBanner.ToArray();
        //}

	
        //[TransactionBound]
        //public virtual BannerDTO GetBanner(int p_nBannerID)
        //{
        //    return DAU.GetObject<BannerDTO>(p_nBannerID);
        //}

	
        //[TransactionBound]
        //public virtual int DeleteBanner(int p_nBannerID)
        //{
        //    return DAU._DeleteObject<BannerDTO>(p_nBannerID);
        //}

		
        //[TransactionBound]
        //public virtual BlockDTO[] GetBlockList(int p_nPage, int p_nPageCount)
        //{
        //    DTOCollection<BlockDTO> arrBlock = DAU.GetObjects<BlockDTO>(null, BlockDTO.BLOCKID_FLD, Order.ASC, p_nPage, p_nPageCount);

        //    return arrBlock.ToArray();
        //}       
		
		
        //[TransactionBound]
        //public virtual int UpdateBlock(BlockDTO p_dtoBlock)
        //{
        //    return DAU.SaveSingleObject<BlockDTO>(p_dtoBlock);
        //}
		
	}
}
