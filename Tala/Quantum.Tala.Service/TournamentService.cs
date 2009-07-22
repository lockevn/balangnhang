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

        
        [TransactionBound]
        public virtual int CreateSoi(soiDTO p_dto)
        {
            return DAU.AddObject<soiDTO>(p_dto).id;
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
        //public virtual BlockDTO GetBlock(int p_nBlockID)
        //{
        //    return DAU.GetObject<BlockDTO>(BlockDTO.BLOCKID_FLD, p_nBlockID);
        //}


        
		
		
        //[TransactionBound]
        //public virtual int UpdateBlock(BlockDTO p_dtoBlock)
        //{
        //    return DAU.SaveSingleObject<BlockDTO>(p_dtoBlock);
        //}

		
        //[TransactionBound]
        //public virtual int DeleteBlock(int p_nBlockID)
        //{
        //    //delete banner block assignment
        //    DAU._DeleteObjects<BannerBlockDTO>(BannerBlockDTO.BLOCKID_FLD, p_nBlockID);

        //    //delete all translation
        //    DAU._DeleteObjects<BlockLocalizedDTO>(BlockLocalizedDTO.BLOCKID_FLD, p_nBlockID);

        //    //delete it
        //    DAU._DeleteObject<BlockDTO>(p_nBlockID);

        //    return 0;
        //}

		
        //[TransactionBound]
        //public virtual VwBannerBlockSelectionDTO[] GetBlockBannerList(int p_nBlockID, int p_nPage, int p_nItemPerPage)
        //{
        //    DTOCollection<VwBannerBlockSelectionDTO> arrResult = DAU.GetObjects<VwBannerBlockSelectionDTO>(VwBannerBlockSelectionDTO.BLOCKID_FLD, p_nBlockID,VwBannerBlockSelectionDTO.BANNERID_FLD,Order.ASC,p_nPage,p_nItemPerPage);
        //    return arrResult.ToArray();
        //}

		
        //[TransactionBound]
        //public virtual int GetBlockBannerPageCount(int p_nBlockID, int p_nItemPerPage)
        //{
        //    Expression expFilter = new Expression(
        //        new FieldOperand(VwBannerBlockSelectionDTO.BLOCKID_FLD),
        //        Operator.Eq,
        //        new ConstantOperand(p_nBlockID));

        //    int nCount = (int)DAU.GetAggregateValue<VwBannerBlockSelectionDTO>(string.Empty, expFilter, Aggregation.Count);
        //    return this.CalculatePageCount(nCount, p_nItemPerPage);
        //}

        //[TransactionBound]
        //public virtual int UpdateBanner(BannerDTO p_dtoBanner)
        //{
        //    return DAU.SaveSingleObject<BannerDTO>(p_dtoBanner);
        //}
	}
}
