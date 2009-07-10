using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
    /// <summary>
    /// Giải đấu
    /// </summary>    
    [Serializable]
    [PersistenceClass("game_tournament", "id", true)]
    public class TournamentDTO : DTOBase
    {
		public const string ID_FLD = "id";
        public const string NAME_FLD = "name";
        public const string FROMTIME_FLD = "fromtime";
        public const string TOTIME_FLD = "totime";
        public const string ISSTART_FLD = "isstart";
        public const string ISENABLED_FLD = "isenabled";
        public const string MINREQUIREDPLAYER_FLD = "minrequiredplayer";
        public const string STARTUPPOINT_FLD = "startuppoint";
		

		private int m_nID;
		private string m_sDescription;
		private int m_nMediaTypeID;
		private string m_sMedia;
		private string m_sLink;
		private string m_sExternalMedia;

		public TournamentDTO()
		{
		}
		
		[PersistenceProperty("id",true,true)]
		public int ID
		{
			get
			{
				return m_nID;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nID = value;
			}
		}

        //[PersistenceProperty("name", false, false)]
        //public string Description
        //{
        //    get
        //    {
        //        return m_sDescription;
        //    }
        //    set
        //    {
        //        m_htIsNull["Description"] = false;
        //        m_sDescription = value;
        //    }
        //}

        //[PersistenceProperty("fromtime", false, false)]
        //public int MediaTypeID
        //{
        //    get
        //    {
        //        return m_nMediaTypeID;
        //    }
        //    set
        //    {
        //        m_htIsNull["MediaTypeID"] = false;
        //        m_nMediaTypeID = value;
        //    }
        //}

        //[PersistenceProperty("totime", false, false)]
        //public string Media
        //{
        //    get
        //    {
        //        return m_sMedia;
        //    }
        //    set
        //    {
        //        m_htIsNull["Media"] = false;
        //        m_sMedia = value;
        //    }
        //}

        //[PersistenceProperty("isstart", false, false)]
        //public string Link
        //{
        //    get
        //    {
        //        return m_sLink;
        //    }
        //    set
        //    {
        //        m_htIsNull["Link"] = false;
        //        m_sLink = value;
        //    }
        //}

        //[PersistenceProperty("isenabled", false, false)]
        //public string ExternalMedia
        //{
        //    get
        //    {
        //        return m_sExternalMedia;
        //    }
        //    set
        //    {
        //        m_htIsNull["ExternalMedia"] = false;
        //        m_sExternalMedia = value;
        //    }
        //}

        //[PersistenceProperty("minrequiredplayer", false, false)]
        //public string ExternalMedia
        //{
        //    get
        //    {
        //        return m_sExternalMedia;
        //    }
        //    set
        //    {
        //        m_htIsNull["ExternalMedia"] = false;
        //        m_sExternalMedia = value;
        //    }
        //}

        //[PersistenceProperty("startuppoint", false, false)]
        //public string ExternalMedia
        //{
        //    get
        //    {
        //        return m_sExternalMedia;
        //    }
        //    set
        //    {
        //        m_htIsNull["ExternalMedia"] = false;
        //        m_sExternalMedia = value;
        //    }
        //}

        //public string IsNullSerializedString
        //{
        //    get
        //    {
        //        return GetIsNullSerializedString();
        //    }
        //    set
        //    {
        //        SetIsNullSerializedString(value);
        //    }
        //}
	}
}
